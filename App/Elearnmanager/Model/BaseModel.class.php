<?php
/**
 * Created by qinguoyong.
 * User: zzvcom
 * Date: 13-06-21
 */
class BaseModel extends Model
{

    protected function tableBaseQuestions()
    {
        return new Model('base_questions');
    }

    protected function tableBaseQuestionsItmes()
    {
        return new Model('base_qestions_itmes');
    }

    protected function tableBaseQuestionsAnswer()
    {
        return new Model('base_questions_answer');
    }

    protected function tableBaseQuestionsGrade()
    {
        return new Model('base_questions_grade');
    }

    protected function tableBaseQuestionsKeys()
    {
        return new Model('base_questions_keys');
    }

    protected function tableExams()
    {
        return new Model('exams');
    }
	
	protected function tableExamsPaper()
    {
        return new Model('exams_paper');
    }
	
	protected function tableExamsQuestions()
    {
        return new Model('exams_questions');
    }
	
	
	protected function tableExamsQuestionsItmes()
    {
        return new Model('exams_questions_itmes');
    }
	
	protected function tableExamsQuestionsAnswer()
    {
        return new Model('exams_questions_answer');
    }
}