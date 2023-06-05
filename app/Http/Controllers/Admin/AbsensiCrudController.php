<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AbsensiRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AbsensiCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AbsensiCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Absensi::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/absensi');
        CRUD::setEntityNameStrings('absensi', 'Absensi');
        $this->crud->orderBy('tanggal', 'desc');

        $this->crud->enableExportButtons();
        
        // $this->crud->denyAccess(['create', 'delete', 'update', 'show', 'line']);
        $this->crud->denyAccess(['create']);
    
    
    }
    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        $this->crud->setDefaultPageLength(50);
        $this->crud->setPageLengthMenu([100, 200, 300]);

        $this->crud->removeAllButtonsFromStack('line');
        
        CRUD::addColumns([
            [
                'name' => 'tanggal',
                'label' => 'Tanggal, Waktu',
                'type' => 'datetime'
            ],
            [
                'name' => 'karyawan',
                'label' => 'Nama',
                'type' => 'select',
                'entity' => 'karyawan',
                'attribute' => 'nama'
            ],
            [
                'name' => 'tipe',
                'label' => 'Tipe',
                'type' => 'select_from_array',
                'options'     => [1 => 'In', 2 => 'Out']
            ],
            [
                'name' => 'is_success',
                'label' => 'Success',
                'type' => 'select_from_array',
                'options'     => [0 => 'No', 1 => 'Yes']
            ],
            [
                'name' => 'latitude',
                'label' => 'Latitude',
                'type' => 'text'
            ],
            [
                'name' => 'longitude',
                'label' => 'Longitude',
                'type' => 'text'
            ]
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AbsensiRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
