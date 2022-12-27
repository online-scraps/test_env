<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\WordInflector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Pluralizer;

class GenerateCRUD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto CRUD Generation ...';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base_path = \base_path();

        // get all models
        // $models[]= \scandir($base_path.'/app/Models');
        
        //get all objects from model.json
        $datas = json_decode(\file_get_contents(\base_path('models.json')));


        foreach((array)$datas as $key=>$data){
            // if(\str_starts_with($model,'.')) continue;  //ignore '.' and '..' files
            
            //convert plural table name to singular
            $table_name = $key;
            $singular =Pluralizer::singular($table_name);

            $columns = (array)$data->columns;

            //build model name from table name
            $model_name =Inflector::classify($singular);

            // $model_name = \substr($model,0,-4);
            // check and register models in external json file for tracking crud generation
            $is_registered = false;
            $json_array = json_decode(\file_get_contents(\base_path('registered_models.json')));

            if(in_array($model_name,$json_array)){
                $is_registered = true;
            }else{
                array_push($json_array,$model_name);
                file_put_contents(\base_path('registered_models.json'),json_encode($json_array));
            }

            // $entity ='App\Models\\'.$model_name;
            // $entity = new $entity();
            // $fillables = $entity->getFillable();

            if(!$is_registered)
            {
                $this->info("\nBuilding migration, model, controller and request file for : ==> ".$model_name);
                //create migrations for given model;
                    $this->info("\nGenerating Migration ...");
                    $cmd="php artisan make:migration create_{$table_name}_table";
                    $output=shell_exec($cmd);
                    //  check if model exist
                    if (strpos($output, 'already exists') !== false) {
                        $this->error('Migration Already exists.');
                    }else{
                        $this->info($output);
                  

                    $output_array = preg_split('/\s+/', trim($output));

                    // if migration file is created then add, migration to schema
                        $migration_file_name = $output_array[2];
                        $migration_file_path  = $base_path.'/database/migrations/'.$migration_file_name.'.php';

                        $string_to_replace = '$table->timestamps();';
                        $file_contents = file_get_contents($migration_file_path);

                        $new_string = "\n \t \t \t";
                        $foreign_string = '';

                        //new columns
                        foreach($columns as $column_name=>$column_attr)
                        {
                            $column_attr_array = explode(":",$column_attr);

                            //build migration attributes according to attributes

                            //check for data types
                            $data_type = $this->prepareDataType($column_attr_array);
                            $attr_name = "'".$column_name."'";
                            $nullable = '';
                            $default = '';
                            //check for nullable
                            if(in_array('nullable',$column_attr_array)){
                                $nullable = '->nullable()';
                            }

                            //check for default
                            if($data_type == 'boolean'){
                                $default = '->default(true)';
                            }
                            //check for display_order
                            if($column_name == 'display_order'){
                                $default = '->default(0)';
                            }
                            //check for foreign key relation
                            if(in_array('foreign',$column_attr_array)){
                                foreach($column_attr_array as $key=>$item){
                                    if(\strpos($item,'table') !== FALSE){
                                      $f_table_name = "'".\str_replace('table-','',$item)."'";
                                      $id = "'id'";
                                      $cascade = "'cascade'";
                                    }
                                }
                                $foreign_string.='$table->foreign('.$attr_name.')->references('.$id.')->on('.$f_table_name.')->onDelete('.$cascade.');'."\n \t \t \t";
                            }
                            $new_string.='$table->'.$data_type.'('.$attr_name.')'.$nullable.$default.';' ."\n \t \t \t";
                        }

                        $new_string .= '$table->timestamps();'."\n\n\t\t\t";
                        //created_by
                        $new_string .= '$table->unsignedSmallInteger('."'created_by'".');'."\n\t\t\t";
                        //updated_by
                        $new_string .= '$table->unsignedSmallInteger('."'updated_by'".')->nullable();'."\n\t\t\t";
                        //deleted_by
                        $new_string .= '$table->unsignedSmallInteger('."'deleted_by'".')->nullable();'."\n\t\t\t";
                        //deleted_uq_code
                        $new_string .= '$table->unsignedInteger('."'deleted_uq_code'".')->default(1);'."\n\t\t\t";
                        //deleted_uq_code
                        $new_string .= '$table->timestamp('."'deleted_at'".')->nullable();'."\n\t\t\t";

                        //append foreign key relation to new string
                        $full_string = $new_string."\n \n \t \t \t".$foreign_string;

                        //replace old content with new content
                        $new_content = str_replace($string_to_replace,$full_string,$file_contents);
                        \file_put_contents($migration_file_path,$new_content);

                        $this->info('Migration created successfully !!');
                    }   
                    
                    
                    //create controller
                    $this->info("\nGenerating Controller ...");
                    $cmd='php artisan backpack:crud-controller '.$model_name;
                    $output=shell_exec($cmd);

                      //  check if model exist
                      if (strpos($output, 'already exists') !== false) {
                        $this->error('Crud Controller Already exists.');
                    }else{
                        $controller_file_path = $base_path.'/app/Http/Controllers/Admin/'.$model_name.'CrudController.php';
                        $controller_file_contents = file_get_contents($controller_file_path);

                        //replace 'extends CrudController to BaseCrudController
                        $controller_file_contents = str_replace('extends CrudController','extends BaseCrudController',$controller_file_contents);

                        //buid fields
                        $fields_to_replace = 'CRUD::setFromDb(); // fields';
                        $new_fields = '$fields=['."\n \t \t \t";
                       
                        foreach($columns as $column_name=>$attr){
                            $new_fields.="[\n \t \t \t\t'name'=>"."'".$column_name."', \n \t \t \t\t" ."'type'=>'text',\n \t \t \t\t"."'label'=>trans("."'".$column_name."'),\n \t \t \t\t";
                            $new_fields.="'wrapper'=>[\n \t \t \t \t\t'class'=>'form-group col-md-4',\n \t \t \t \t], \n \t \t \t], \n \t \t \t";
                        }
                        $new_fields.="]; \n \n \t \t";
                        $new_fields.='$this->crud->addFields(array_filter($fields));';


                        //buid columns
                        $columns_to_replace = 'CRUD::setFromDb(); // columns';
                        $new_columns = '$columns=['."\n \t \t \t";
                       
                        foreach($columns as $column_name=>$attr){
                            $new_columns.="[\n \t \t \t\t'name'=>"."'".$column_name."', \n \t \t \t\t" ."'type'=>'text',\n \t \t \t\t"."'label'=>trans("."'".$column_name."'),\n \t \t \t], \n\t\t\t";
                        }
                        $new_columns.="];\n \n \t \t";
                        $new_columns.='$this->crud->addColumns(array_filter($columns));';

                        $new_content_fields = str_replace($fields_to_replace,$new_fields,$controller_file_contents);
                        $new_content = str_replace($columns_to_replace,$new_columns,$new_content_fields);
                        \file_put_contents($controller_file_path,$new_content);
                        $this->info("Controller created successfully !!");
                    }


                // model creation
                    $this->info("\nGenerating Model ...");
                    $cmd='php artisan make:model '.$model_name;
                    $output=shell_exec($cmd);

                    //  check if model exist
                    if (strpos($output, 'already exists') !== false) {
                        $this->error('Model Already exists.');
                    }else{
                        $model_file_path = $base_path.'/app/Models/'.$model_name.'.php';
                        $model_file_contents = file_get_contents($model_file_path);

                        //replace 'extends Model to BaseModel
                        $model_file_contents = str_replace('extends Model','extends BaseModel',$model_file_contents);

                        $string_to_replace = 'use HasFactory;';
                        $new_string = 'protected $table='."'".$table_name."';\n \t";
                        $fillables = '';
                        //make columns as string
                        foreach($columns as $column_name=>$attr){
                            $fillables.="'".$column_name."',";
                        }
                        $fillables.="'deleted_by','deleted_at','deleted_uq_code'";
                        $new_string.= 'protected $fillable=['.$fillables.'];'."\n"; 
                        $new_content = str_replace($string_to_replace,$new_string,$model_file_contents);
                        \file_put_contents($model_file_path,$new_content);
                        $this->info("Model created successfully !!");
                    }


                //create request
                $this->info("\nGenerating Request ...");
                $cmd='php artisan backpack:crud-request '.$model_name;
                $output=shell_exec($cmd);

                //  check if model exist
                if (strpos($output, 'already exists') !== false) {
                    $this->error('Request Already exists.');
                }else{
                    $this->info($output);
                }

                //insert to routes custom/web.php
                $this->info('Adding route to custom.php file ...');
                Artisan::call('backpack:add-custom-route', [
                    'code' => "Route::crud('/".strtolower($model_name)."', '".$model_name."CrudController');",
                ]);

                //insert in sidebar content
                $this->info('Adding route to sidebar content ...');
                $route="{{backpack_url('".strtolower($model_name)."')}}";
                Artisan::call('backpack:add-sidebar-content', [
                    'code' => "<li class='nav-item'><a class='nav-link' href='".$route."'><i class='nav-icon fa fa-question'></i> ".Str::plural($model_name).'</a></li>',
                ]);

                if($this->confirm('Do you wish to continue ?')){
                    continue;
                }else{
                    break;
                }
                
            }else{
                $this->warn("\n".$model_name ." Model is already registered. Please check !! ");
            }
        }

        if($this->confirm('Do you want to run migration ??')){
            $this->info('Running migration ...');
            $output=shell_exec('php artisan migrate');
        }else{
            $this->error('Aborting migration ...');
        }


    }

    public function prepareDataType($column_attributes)
    {
        //string
        if(in_array('string',$column_attributes)) return 'string';

        //uuid
        if(in_array('uuid',$column_attributes)) return 'uuid';

        //integer
        if(in_array('integer',$column_attributes)) return 'unsignedInteger';

        //float
        if(in_array('float',$column_attributes)) return 'float';

        //boolean
        if(in_array('boolean',$column_attributes)) return 'boolean';

        //date
        if(in_array('date',$column_attributes)) return 'date';

        //nepali_date
        if(in_array('nepali_date',$column_attributes)) return 'string';

        //text
        if(in_array('text',$column_attributes)) return 'text';

        //json
        if(in_array('json',$column_attributes)) return 'json';

        //foreign
        if(in_array('foreign',$column_attributes)) return 'unsignedSmallInteger';
    }
}
