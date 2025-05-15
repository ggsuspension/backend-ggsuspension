<?php

namespace App\Http\Controllers;

use App\Models\ServiceCustomer;
use Illuminate\Http\Request;

class ServiceCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=ServiceCustomer::create($request->all());
        return response()->json(['success'=>true,'data' => $data], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCustomer $serviceCustomer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCustomer $serviceCustomer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCustomer $serviceCustomer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCustomer $serviceCustomer)
    {
        //
    }
}
