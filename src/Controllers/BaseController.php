<?php
namespace plokko\BaseController\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use plokko\FormBuilder\FormBuilder;
use Route;

class BaseController extends Controller
{
    protected static
        $model,
        $name='basecontroller',
        
        $viewBase=null,
        $transBase=null,
        $routes=['index','show','create','store','edit','update', 'destroy'];

    protected function view($name,array $args=[])
    {
        if (static::$viewBase && view()->exists(static::$viewBase.'.'.$name) )
            return view(static::$viewBase.'.'.$name,$args);

        if (view()->exists(static::$name.'.'.$name) )
            return view(static::$name.'.'.$name,$args);

        return view('basecontroller.'.$name,$args);
    }

    /**
     * @param string $txt
     * @param array $parameters
     * @return mixed
     */
    protected function trans($txt, array $parameters = [])
    {
        $base=static::$transBase?static::$transBase:static::$name;
        $r=trans("$base.$txt",$parameters);
        if($r=="$base.$txt")
            $r=trans("basecontroller.$txt",$parameters);
        return $r;
    }


    protected function route($name)
    {
        return route($this->routeName($name));
    }

    protected function form(FormBuilder &$fb)
    {
        //Auto create form//
        $m=self::Model();
        foreach($m->getFillable() AS $fld)
        {
            $fb->text($fld);
        }
    }
    protected final function getForm($action)
    {
        switch($action)
        {
            case 'create':
                return $this->form(FormBuilder::make([
                    'route'=>$this->routeName('store'),
                    'method'=>'PUT',
                ]));
            case 'update':

                return $this->form(FormBuilder::make([
                    'route'=>$this->routeName('update'),
                    'method'=>'PUT',
                ]));
            default:
            case 'delete':return null;
        }
    }

    protected function getViewData($action)
    {
        return [
            'self'=>$this,
            'form'=>$this->getForm($action),
        ];
    }

    /**
     * @return Model
     */
    protected function Model()
    {
        return new static::$model();
    }
    
    protected function routeName($name)
    {
        return (static::$name.'.'.$name);
    }


    ///

    public static function registerRoutes()
    {
        Route::resource(static::$name,static::class, ['only' => static::$routes]);
    }

    ///

    public function index()
    {
        return $this->view('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->view('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->view('edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $m=self::Model();
        $e=$m->findOrFail($id);
        $e->delete();
    }
}
