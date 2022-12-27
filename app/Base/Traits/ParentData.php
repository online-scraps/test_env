<?php
namespace App\Base\Traits;


/**
 *
 */
trait ParentData
{


    public function parent($name)
    {
        // dd($this->crud);
        $request = $this->request;
        return $request->route($name) ?? null;
    }

    public function setUpLinks($methods = ['index'])
    {
        $currentMethod = $this->crud->getActionMethod();
        $exits = method_exists($this, 'tabLinks');
        if ($exits && in_array($currentMethod, $methods)) {
            $this->data['tab_links'] = $this->tabLinks();
        }
    }

    public function setProjectCategoryTabs()
    {
        $parameters = array_values(request()->route()->parameters);

        $links = [];
        $links[] = ['label' => trans('menu.projectcategory'), 'href' => backpack_url('mstprojectcategory/'.$parameters[0].'/edit')];
        $links[] = ['label' => trans('menu.projectsubcategory'), 'href' => backpack_url('mstprojectcategory/'.$parameters[0].'/mstprojectsubcategory')];
        return $links;

    }

    public function setExecutingEntityTabs()
    {
        $parameters = array_values(request()->route()->parameters);

        $links = [];
        $links[] = ['label' => trans('menu.executingentitytypes'), 'href' => backpack_url('mstexecutingentitytype/'.$parameters[0].'/edit')];
        $links[] = ['label' => trans('menu.executingentity'), 'href' => backpack_url('mstexecutingentitytype/'.$parameters[0].'/mstexecutingentity')];
        return $links;

    }


    public function setPtProjectTabs()
    {
        $parameters = array_values(request()->route()->parameters);

        $links = [];
        $links[] = ['label' => trans('menu.project'), 'href' => backpack_url('ptproject/'.$parameters[0].'/edit')];
        $links[] = ['label' => trans('menu.projectfiles'), 'href' => backpack_url('ptproject/'.$parameters[0].'/ptprojectfiles')];
        $links[] = ['label' => trans('menu.projectnotes'), 'href' => backpack_url('ptproject/'.$parameters[0].'/ptprojectnotes ')];
        return $links;

    }

    public function enableDialog($enable = true)
    {
        $this->data['controller'] = (new \ReflectionClass($this))->getShortName();
        $this->crud->controller = $this->data['controller'];
        $this->enableDialog = $enable;
        $this->data['enableDialog'] = property_exists($this, 'enableDialog') ? $this->enableDialog : false;
        $this->crud->enableDialog = property_exists($this, 'enableDialog') ? $this->enableDialog : false;
    }











    // public function setProjectProgressTabs()
    // {
    //     $parameters = array_values(request()->route()->parameters);

    //     $links = [];
    //     $links[] = ['label' => trans('menu.ptprojectprogress'), 'href' => backpack_url('ptprojectprogress/'.$parameters[0].'/edit')];
    //     $links[] = ['label' => trans('menu.ptprojectprogressfiles'), 'href' => backpack_url('ptprojectprogress/'.$parameters[0].'/ptprojectprogressfiles')];
    //     return $links;

    // }

}
