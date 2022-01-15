<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipper;
use Yajra\DataTables\Facades\DataTables;

class ShipperController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index()
    {
        $shipper= Shipper::All();
        return view('shipper.index',compact('shipper'));
    }*/

    public function index()
    {
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }
 

        if (request()->ajax()) {
            $query = Shipper::All();

        

            $shippers = $query->select(
                'shippers.id',
                'shippers.shipper_name',
                'shippers.type',
                'shippers.tel',
                'shippers.other_details'
           
                );

            

            return Datatables::of($shippers)
 
                ->addColumn(
                    'action',
                    function ($row){
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu"><li><a href="' . action('LabelsController@show') . '?product_id=' . $row->id . '" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fa fa-barcode"></i> ' . __('barcode.labels') . '</a></li>';

                        if (auth()->user()->can()) {
                            $html .=
                            '<li><a href="' . action('ProductController@view', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }

                        if (auth()->user()->can()) {
                            $html .=
                            '<li><a href="' . action('ProductController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('product.delete')) {
                            $html .=
                            '<li><a href="' . action('ProductController@destroy', [$row->id]) . '" class="delete-product"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
   
                ->addColumn('name',  function ($row) {
                    $total_remaining = '';
                    return $total_remaining;})
                ->addColumn('type',  function ($row) {
                    $total_remaining = '';
                    return $total_remaining;})
                 ->addColumn('tel',  function ($row) {
                    $total_remaining = '';
                    return $total_remaining;})
                 ->addColumn('other_details',  function ($row) {
                    $total_remaining = '';
                    return $total_remaining;})
 
         
                ->rawColumns(['action', 'name', 'type', 'tel', 'other_details'])
                ->make(true);
        }



        return view('shipper.index');
           
    }

          /**
     * info pour un article
     * @param $id
     * @return View
     */
    public function about($id){
        $shipper= Shipper::findOrFail($id);
       return view('shipper/about',compact('shipper'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shipper.create');
    }

       /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->filled('shipper_name','type','tel','other_details')){
            $shipper_name=$request->input('shipper_name');
            $type=$request->input('type');
            $tel=$request->input('tel');
            $other_details=$request->input('other_details');
            $o=0;
            
            $shipper=Shipper::firstOrCreate(['shipper_name'=>$shipper_name,'type'=>$type,'tel'=>$tel,'other_details'=>$other_details]);
            return redirect()->route('shipper.index');
            
           
        }
        else{
            return redirect()->route('shipper.index');
        }
        
    }

          /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id){
        $shipper= Shipper::findOrFail($id);
        
      return view('shipper.update',compact('shipper'));
      
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
        
        $shipper= Shipper::findOrFail($id);
        $shipper->update($request->all());
        return redirect()->route('shipper.index');
    }

         /**
     * gestion de supprimation
     * 
     * @return redirect
     */
    public function delete($id){
     
        $shipper= Shipper::findOrFail($id);
        $shipper->delete();
        return redirect()->route('shipper.index');
      
    }

          /**
     * method pour rechercher
     * 
     * @return View
     */
    public function search(Request $request){
        $search= $request->input('search');
        $shipper= Shipper::searchByTitle($search)->get();    //on utilise un scope searchByTitle dands le modele App
        return view('shipper.index',compact('shipper','search'));
    }
}
