<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipper;

class ShipperController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipper= Shipper::All();
        return view('shipper.index',compact('shipper'));
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
        if($request->filled('name','type','tel','other_details')){
            $name=$request->input('name');
            $type=$request->input('type');
            $tel=$request->input('tel');
            $other_details=$request->input('other_details');
            
            $shipper=Shipper::firstOrCreate(['name'=>$name,'type'=>$type,'tel'=>$tel,'other_details'=>$other_details]);
            return redirect()->route('shipper.index');
            
           
        }
        else{
            return redirect()->route('store.address');
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
