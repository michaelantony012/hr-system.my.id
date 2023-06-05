<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\KaryawanRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class KaryawanCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class KaryawanCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Karyawan::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/karyawan');
        CRUD::setEntityNameStrings('Karyawan', 'Master Karyawan');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->denyAccess(['show', 'delete']);

        // CRUD::column('id')->type('number');
        CRUD::column('nik')->type('text');
        CRUD::column('nama')->type('text');
        CRUD::addColumn([
            'label'     => 'Shift', // Table column heading
            'type'      => 'select',
            'name'      => 'shift_id', // the column that contains the ID of that connected entity;
            'entity'    => 'shift', // the method that defines the relationship in your Model
            'attribute' => 'nama_shift', // foreign key attribute that is shown to user
            'model'     => "App\Models\Shifts", // foreign key model
        ]);
        CRUD::addColumn([
            'label'     => 'User Name', // Table column heading
            'type'      => 'select',
            'name'      => 'user_id', // the column that contains the ID of that connected entity;
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => "App\Models\User", // foreign key model
        ]);
        CRUD::addColumns([
            [
                'name' => 'device_id',
                'type' => 'text',
                'label' => 'Device ID',
            ],
            [
                'name' => 'is_active',
                'type' => 'select_from_array',
                'label' => 'Is Active',
                'options'     => [0 => 'No', 1 => 'Yes']
            ],
        ]);
        CRUD::column('tanggal_join')->type('date');
        CRUD::orderBy('nik');
        

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
        //     CRUD::setValidation([
        //         'nik' => 'required|min:6',
        //         'nama' => 'required'
        //     ]);
        CRUD::setValidation(KaryawanRequest::class);

        // CRUD::field('nama')->type('text');
        CRUD::addField(['name' => 'nik', 'label' => 'Nomor Induk', 'type' => 'text']); 
        CRUD::addField(['name' => 'nama', 'label' => 'Nama lengkap', 'type' => 'text']);
        CRUD::addField(['name' => 'tanggal_join', 'label' => 'Tanggal Bergabung', 'type' => 'date']);
        CRUD::addField(['name' => 'is_active', 'type' => 'boolean', 'default' => 1]);
        CRUD::addField(['name' => 'device_id', 'label' => 'Device ID', 'type' => 'text']);

        CRUD::field('user_id')->type('select2')->model('App\Models\User')->attribute('name')->entity('user'); // notice the name is the foreign key attribute

        CRUD::field('shift_id')->type('select2')->model('App\Models\Shifts')->attribute('nama_shift')->entity('shift'); // notice the name 

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
