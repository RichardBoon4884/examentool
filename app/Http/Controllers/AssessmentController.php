<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\FinalAssessment;
use App\DeterminedExam;

class AssessmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Start assement
     *
     * @param $exam_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function startAssessment($exam_id)
    {
        //Find if exam exists
        if($determined_exam = DeterminedExam::find($exam_id)->first()) {

            //Make final assessment
            $final_assessment = new FinalAssessment();

            $final_assessment->exam_title = $determined_exam->exam_title;
            $final_assessment->exam_description = $determined_exam->exam_description;
            $final_assessment->student_number = "";
            $final_assessment->examinators = array();
            $final_assessment->exam_cohort = $determined_exam->exam_cohort;
            $final_assessment->determined_exam_id  = $determined_exam->_id;
            $final_assessment->exam_rating_algorithms  = $determined_exam->exam_rating_algorithms;
            $final_assessment->exam_criteria  = $determined_exam->exam_criteria;
            $final_assessment->result = "";
            $final_assessment->finished = False;
            $final_assessment->date = date();

            //Insert Final assessment
            if($final_assessment->save()) {
                //Make copy of FinalAssessment for assessment file
                $assessment = new Assessment();

                $assessment->exam_title = $final_assessment->exam_title;
                $assessment->exam_description = $final_assessment->exam_description;
                $assessment->student_number = $final_assessment->student_number;
                $assessment->examinators = "";//Insert current user id or object when user system integrated!!
                $assessment->exam_cohort = $final_assessment->exam_cohort;
                $assessment->final_assessment_id = $final_assessment->_id;
                $assessment->exam_rating_algorithms = $final_assessment->exam_rating_algorithms;
                $assessment->exam_criteria = $final_assessment->exam_criteria;
                $assessment->finished = False;
                $assessment->date = $final_assessment->date;

                //Insert assessment
                if($assessment->save()) {
                    //Return assessment
                    return response()->json($assessment, 201);
                } else {
                    //Return 500, error saving
                    return response()->json(array(), 500);
                }

            } else {
                //Return 500, error saving
                return response()->json(array(), 500);
            }

        } else {
            //Return 404, entry not found
            return response()->json(array(), 404);
        }
    }

    /**
     * Get all FinalAssessments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllFinalAssessments() {
        if($data = FinalAssessment::all()) {
            //return all trimmed Exams, 200
            return response()->json($data, 200);
        } else {
            //return 500
            return response()->json(array(), 500);
        }
    }

    /**
     * Hook in on a running assessment
     *
     * @param $final_assessment_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hookInOnAssessment($final_assessment_id) {
        //Find FinalAssessment
        if($final_assessment = FinalAssessment::find($final_assessment_id)->first()) {
            //If FinalAssessment is finished, return 403
            if($final_assessment->finished == true){
                return response()->json(array(), 403);
            } else {
                //Make empty assessment
                $assessment = new Assessment();

                //Set data in assessment
                $assessment->exam_title = $final_assessment->exam_title;
                $assessment->exam_description = $final_assessment->exam_description;
                $assessment->student_number = $final_assessment->student_number;
                $assessment->examinators = "";//Insert current user id or object when user system integrated!!
                $assessment->exam_cohort = $final_assessment->exam_cohort;
                $assessment->final_assessment_id = $final_assessment->_id;
                $assessment->exam_rating_algorithms = $final_assessment->exam_rating_algorithms;
                $assessment->exam_criteria = $final_assessment->exam_criteria;
                $assessment->finished = False;
                $assessment->date = $final_assessment->date;

                //Insert assessment
                if($assessment->save()) {
                    //Return assessment
                    return response()->json($assessment, 201);
                } else {
                    //Return 500, error saving
                    return response()->json(array(), 500);
                }
            }
        } else {
            //Return 404 if no FinalAssessment found
            return response()->json(array(), 404);
        }
    }

}