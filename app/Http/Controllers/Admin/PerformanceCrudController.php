<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PerformanceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\Performance;
use App\Models\PerformanceDetail;
use Illuminate\Support\Facades\Auth;

/**
 * Class PerformanceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PerformanceCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Performance::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/performance');
        CRUD::setEntityNameStrings('performance', 'Penilaian Kinerja');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'id', 'type' => 'number']); 
        CRUD::addColumn(['name' => 'id_karyawan', 'type' => 'number']); 
        CRUD::addColumn(['name' => 'tanggal', 'type' => 'date']); 

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
        CRUD::setValidation(PerformanceRequest::class);

        CRUD::addField(['name' => 'id_karyawan', 'type' => 'number']);
        CRUD::addField(['name' => 'tanggal', 'type' => 'date']);
        // CRUD::field('items')->type('repeatable')->subfields([
        //     [
        //         'name'    => 'id_skor',
        //         'type'    => 'number',
        //         'label'   => 'Skor',
        //         'wrapper' => [
        //             'class' => 'form-group col-md-4',
        //         ],
        //     ],
        //     [
        //         'name'       => 'id_kriteria',
        //         'type'       => 'number',
        //         'label'      => 'Kriteria',
        //         // 'attributes' => ['step' => 'any'],
        //         'wrapper'    => [
        //             'class' => 'form-group col-md-4',
        //         ],
        //     ],
        // ]);

        CRUD::addField([   // repeatable
            'name'  => 'items',
            'label' => 'Details',
            'type'  => 'repeatable',
            'fields' => [
                [
                    // 'name'    => 'id_kriteria',
                    // 'type'    => 'number',
                    'name'    => 'id_kriteria',
                    'type'    => 'select2',
                    'model'   => 'App\Models\Kriteria',
                    'label'   => 'Kriteria ID',
                    'attribute' => 'nama_kriteria',
                    'entity'  => 'kriteria',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'id_skor',
                    'type'    => 'number',
                    'label'   => 'Skor ID',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [  
                    'name'  => 'id',
                    'type'  => 'number',
                    'label' => 'id',
                    // 'value' => '0',
                    'type'  => 'hidden'
                ],
            ],
        
            // optional
            'new_item_label'  => 'Add Items', // customize the text of the button
            // 'init_rows' => 2, // number of empty rows to be initialized, by default 1
            // 'min_rows' => 2, // minimum rows allowed, when reached the "delete" buttons will be hidden
            // 'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden
        
        ]);

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

    
    public function store(PerformanceRequest $request)
    {
        $input = $request->all();
        $items = json_decode($input['items']);
        // print_r($option);
        $user = Auth::user();
        $performance = new Performance();
        $performance->id_karyawan = $input['id_karyawan'];
        $performance->tanggal = $input['tanggal'];
        $performance->save();

        if($items!=''){
            foreach($items as $item){
				
                $detail = new PerformanceDetail();
                $detail->id_kriteria = $item->id_kriteria;
                $detail->id_skor = $item->id_skor;
                $detail->performance_id = $performance->id;
                $detail->save();
            }
        }
		//  DB::select('CALL ExamNo_Generate('.$exam->id.')');
 
        return redirect('performance/'.$performance->id.'/show');
    }

    public function update(PerformanceRequest $request,  $id)
    {
        $input = $request->all();
        $items = json_decode($input['items']);
        $user = Auth::user();
        $performance = Performance::where('id',$id)->first();
        $performance->id_karyawan = $input['id_karyawan'];
        $performance->tanggal = $input['tanggal'];
        $performance->save(); 
		
        if($items!=''){
			$length=count($items);
			$i=0;
			$detail_id = array();
			while($i<$length){
				$detail_id[]=$items[$i]->id;
				$i++;
			}
            foreach($items as $item){
				$delete = PerformanceDetail::whereNotIn('id',$detail_id)->where('performance_id',$performance->id)->delete();
				if($item->id==0){
					$detail = new PerformanceDetail();
                	$detail->id_kriteria = $item->id_kriteria;
                	$detail->id_skor = $item->id_skor;
                	$detail->performance_id = $performance->id;
                	$detail->save();
				}else{
				$detail = PerformanceDetail::where('performance_id',$performance->id)->where('id',$item->id)
					->update([
						'id_kriteria' => $item->id_kriteria,
						'id_skor' => $item->id_skor,
						'performance_id' => $performance->id
					]);
				}
            }
        }
		
        return redirect('performance/'.$performance->id.'/show');
    }
    

    /*
    public function store(PerformanceRequest $request)
    {
        $input = $request->all();
        $option = json_decode($input['items']);
        dd($option);
    }
    public function update(PerformanceRequest $request)
    {
        $input = $request->all();
        $option = json_decode($input['items']);
        // dd($option);
        $performance = Performance::find($input['id']);
        // dd($performance);
        // dd($performance['created_at']);
        $performance->id_karyawan = $input['id_karyawan'];
        $performance->tanggal = $input['tanggal'];
        $performance->save();
    }

    public function update(ExamRequest $request,  $id)
    {
        $input = $request->all();
        $option = json_decode($input['questions']);
        $user = Auth::user();
        $exam = Exam::where('id',$id)->first();
        // $exam->exam_no = $input['exam_no'];
        $exam->category_id = $input['category_id'];
        $exam->exam_rule = $input['exam_rule'];
        $exam->exam_name = $input['exam_name'];
        $exam->save(); 
		
        if($option!=''){
			$length=count($option);
			$i=0;
			$question_id = array();
			while($i<$length){
				$question_id[]=$option[$i]->question_id;
				$i++;
			}
            foreach($option as $options){
				//dd($options);
                //$questions = new Question();
                //$questions->question_no = $options->question_no;
                //$questions->question_desc1 = $options->question_desc1;
                //$questions->question_desc2 = $options->question_desc2;
                //$questions->question_type = $options->question_type;
                //$questions->company_id = $user->company_id;
                //$questions->exam_id = $exam->id;
                //$questions->save();
				$delete = Question::whereNotIn('id',$question_id)->where('exam_id',$exam->id)->delete();
				if($options->question_id==0){
					$questions = new Question();
                	$questions->question_no = $options->question_no;
                	$questions->question_desc1 = $options->question_desc1;
                	$questions->question_desc2 = $options->question_desc2;
                	$questions->question_type = $options->question_type;
                	$questions->company_id = $user->company_id;
                	$questions->exam_id = $exam->id;
                	$questions->save();
				}else{
				$questions = Question::where('exam_id',$exam->id)->where('id',$options->question_id)
					->update([
						'question_no' => $options->question_no,
						'question_desc1' => $options->question_desc1,
						'question_desc2' => $options->question_desc2,
						'question_type'  => $options->question_type,
						'company_id'     => $user->company_id,
						'exam_id'     	 => $exam->id
					]);
				}
            }
        }
		
        return redirect('performance/'.$exam->id.'/show');
    }
    */
}
