<?php
/* For licensing terms, see /license.txt */

/**
 * @author Claro Team <cvs@claroline.net>
 * @author Yannick Warnier <yannick.warnier@beeznest.com> - updated ImsAnswerHotspot to match QTI norms
 * @package chamilo.exercise
 */
class Ims2Question extends Question
{
    /**
     * Include the correct answer class and create answer
     * @return Answer
     */
    public function setAnswer()
    {
        switch($this->type) {
            case MCUA:
                $answer = new ImsAnswerMultipleChoice($this->id);

                return $answer;
            case MCMA:
                $answer = new ImsAnswerMultipleChoice($this->id);

                return $answer;
            case TF :
                $answer = new ImsAnswerMultipleChoice($this->id);

                return $answer;
            case FIB:
                $answer = new ImsAnswerFillInBlanks($this->id);

                return $answer;
            case MATCHING:
                //no break
            case MATCHING_DRAGGABLE:
                $answer = new ImsAnswerMatching($this->id);

                return $answer;
            case FREE_ANSWER:
                $answer = new ImsAnswerFree($this->id);

                return $answer;
            case HOT_SPOT:
                $answer = new ImsAnswerHotspot($this->id);

                return $answer;
            default:
                $answer = null;
                break;
        }

        return $answer;
    }

    function createAnswersForm($form)
    {
    	return true;
    }

    function processAnswersCreation($form)
    {
    	return true;
    }
}
/**
 * Class
 * @package chamilo.exercise
 */
class ImsAnswerMultipleChoice extends Answer
{
    /**
     * Return the XML flow for the possible answers.
     *
     */
    public function imsExportResponses($questionIdent, $questionStatment)
    {
        // @todo getAnswersList() converts the answers using api_html_entity_decode()
		$this->answerList = $this->getAnswersList(true);
        $out  = '    <choiceInteraction responseIdentifier="' . $questionIdent . '" >' . "\n";
        $out .= '      <prompt><![CDATA['.formatExerciseQtiTitle($questionStatment) . ']]></prompt>'. "\n";
		if (is_array($this->answerList)) {
	        foreach ($this->answerList as $current_answer) {
	            $out .= '<simpleChoice identifier="answer_' . $current_answer['id'] . '" fixed="false">
                         <![CDATA['.formatExerciseQtiTitle($current_answer['answer']).']]>';
	            if (isset($current_answer['comment']) && $current_answer['comment'] != '') {
	                $out .= '<feedbackInline identifier="answer_' . $current_answer['id'] . '">
	                         <![CDATA['.formatExerciseQtiTitle($current_answer['comment']).']]>
	                         </feedbackInline>';
	            }
	            $out .= '</simpleChoice>'. "\n";
	        }
		}
        $out .= '    </choiceInteraction>'. "\n";

        return $out;
    }

    /**
     * Return the XML flow of answer ResponsesDeclaration
     *
     */
    public function imsExportResponsesDeclaration($questionIdent)
    {
		$this->answerList = $this->getAnswersList(true);
		$type = $this->getQuestionType();
        if ($type == MCMA)  $cardinality = 'multiple'; else $cardinality = 'single';

        $out = '  <responseDeclaration identifier="' . $questionIdent . '" cardinality="' . $cardinality . '" baseType="identifier">' . "\n";

        // Match the correct answers.

        $out .= '    <correctResponse>'. "\n";
		if (is_array($this->answerList)) {
	        foreach($this->answerList as $current_answer) {
	            if ($current_answer['correct']) {
	                $out .= '      <value>answer_'. $current_answer['id'] .'</value>'. "\n";
	            }
	        }
		}
        $out .= '    </correctResponse>'. "\n";

        //Add the grading

        $out .= '    <mapping>'. "\n";
		if (is_array($this->answerList)) {
	        foreach($this->answerList as $current_answer) {
	            if (isset($current_answer['grade'])) {
	                $out .= ' <mapEntry mapKey="answer_'. $current_answer['id'] .'" mappedValue="'.$current_answer['grade'].'" />'. "\n";
	            }
	        }
		}
        $out .= '    </mapping>'. "\n";
        $out .= '  </responseDeclaration>'. "\n";

        return $out;
    }
}

/**
 * Class
 * @package chamilo.exercise
 */
class ImsAnswerFillInBlanks extends Answer
{
    /**
     * Export the text with missing words.
     *
     *
     */
    public function imsExportResponses($questionIdent, $questionStatment)
    {
		$this->answerList = $this->getAnswersList(true);
        $text = '';
        $text .= $this->answerText;
        if (is_array($this->answerList)) {
            foreach ($this->answerList as $key=>$answer) {
                $key = $answer['id'];
                $answer = $answer['answer'];
                $len = api_strlen($answer);
                $text = str_replace('['.$answer.']','<textEntryInteraction responseIdentifier="fill_'.$key.'" expectedLength="'.api_strlen($answer).'"/>', $text);
            }
        }
        $out = $text;

        return $out;
    }

    /**
     *
     */
    public function imsExportResponsesDeclaration($questionIdent)
    {
		$this->answerList = $this->getAnswersList(true);
		$this->gradeList = $this->getGradesList();
        $out = '';
		if (is_array($this->answerList)) {
	        foreach ($this->answerList as $answer) {
	        	$answerKey = $answer['id'];
	        	$answer = $answer['answer'];
	            $out .= '  <responseDeclaration identifier="fill_' . $answerKey . '" cardinality="single" baseType="identifier">' . "\n";
	            $out .= '    <correctResponse>'. "\n";
                $out .= '      <value><![CDATA['.formatExerciseQtiTitle($answer).']]></value>'. "\n";
	            $out .= '    </correctResponse>'. "\n";
	            if (isset($this->gradeList[$answerKey])) {
	                $out .= '    <mapping>'. "\n";
	                $out .= '      <mapEntry mapKey="'.$answer.'" mappedValue="'.$this->gradeList[$answerKey].'"/>'. "\n";
	                $out .= '    </mapping>'. "\n";
	            }

	            $out .= '  </responseDeclaration>'. "\n";
	        }
		}

       return $out;
    }
}

/**
 * Class
 * @package chamilo.exercise
 */
class ImsAnswerMatching extends Answer
{
    /**
     * Export the question part as a matrix-choice, with only one possible answer per line.
     */
    public function imsExportResponses($questionIdent, $questionStatment)
    {
		$this->answerList = $this->getAnswersList(true);
		$maxAssociation = max(count($this->leftList), count($this->rightList));

        $out = "";

        $out .= '<matchInteraction responseIdentifier="' . $questionIdent . '" maxAssociations="'. $maxAssociation .'">'. "\n";
        $out .= $questionStatment;

        //add left column

        $out .= '  <simpleMatchSet>'. "\n";
		if (is_array($this->leftList)) {
	        foreach ($this->leftList as $leftKey=>$leftElement) {
	            $out .= '
	            <simpleAssociableChoice identifier="left_'.$leftKey.'" >
	                <![CDATA['.formatExerciseQtiTitle($leftElement['answer']).']]>
	            </simpleAssociableChoice>'. "\n";
	        }
    	}

        $out .= '  </simpleMatchSet>'. "\n";

        //add right column

        $out .= '  <simpleMatchSet>'. "\n";

        $i = 0;

		if (is_array($this->rightList)) {
	        foreach($this->rightList as $rightKey=>$rightElement) {
	            $out .= '<simpleAssociableChoice identifier="right_'.$i.'" >
	                    <![CDATA['.formatExerciseQtiTitle($rightElement['answer']).']]>
	                    </simpleAssociableChoice>'. "\n";
	            $i++;
	        }
		}
        $out .= '  </simpleMatchSet>'. "\n";
        $out .= '</matchInteraction>'. "\n";

        return $out;
    }

    /**
     *
     */
    public function imsExportResponsesDeclaration($questionIdent)
    {
		$this->answerList = $this->getAnswersList(true);
        $out =  '  <responseDeclaration identifier="' . $questionIdent . '" cardinality="single" baseType="identifier">' . "\n";
        $out .= '    <correctResponse>' . "\n";

        $gradeArray = array();
		if (is_array($this->leftList)) {
	        foreach ($this->leftList as $leftKey=>$leftElement) {
	            $i=0;
	            foreach ($this->rightList as $rightKey=>$rightElement) {
	                if (($leftElement['match'] == $rightElement['code'])) {
	                    $out .= '      <value>left_' . $leftKey . ' right_'.$i.'</value>'. "\n";

	                    $gradeArray['left_' . $leftKey . ' right_'.$i] = $leftElement['grade'];
	                }
	                $i++;
	            }
	        }
		}
        $out .= '    </correctResponse>'. "\n";
        $out .= '    <mapping>' . "\n";
        if (is_array($gradeArray)) {
	        foreach ($gradeArray as $gradeKey=>$grade) {
	            $out .= '          <mapEntry mapKey="'.$gradeKey.'" mappedValue="'.$grade.'"/>' . "\n";
	        }
        }
        $out .= '    </mapping>' . "\n";
        $out .= '  </responseDeclaration>'. "\n";

        return $out;
    }
}

/**
 * Class
 * @package chamilo.exercise
 */
class ImsAnswerHotspot extends Answer
{
    /**
     * TODO update this to match hot spots instead of copying matching
     * Export the question part as a matrix-choice, with only one possible answer per line.
     */
    public function imsExportResponses($questionIdent, $questionStatment, $questionDesc='', $questionMedia='')
    {
		$this->answerList = $this->getAnswersList(true);
		$questionMedia = api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$questionMedia;
		$mimetype = mime_content_type($questionMedia);
		if(empty($mimetype)){
			$mimetype = 'image/jpeg';
		}

		$text = '      <p>'.$questionStatment.'</p>'."\n";
		$text .= '      <graphicOrderInteraction responseIdentifier="hotspot_'.$questionIdent.'">'."\n";
		$text .= '        <prompt>'.$questionDesc.'</prompt>'."\n";
		$text .= '        <object type="'.$mimetype.'" width="250" height="230" data="'.$questionMedia.'">-</object>'."\n";
        if (is_array($this->answerList)) {
	        foreach ($this->answerList as $key=>$answer) {
	        	$key = $answer['id'];
	        	$answerTxt = $answer['answer'];
	        	$len = api_strlen($answerTxt);
	        	//coords are transformed according to QTIv2 rules here: http://www.imsproject.org/question/qtiv2p1pd/imsqti_infov2p1pd.html#element10663
	        	$coords = '';
	        	$type = 'default';
	        	switch($answer['hotspot_type']){
	        		case 'square':
	        			$type = 'rect';
						$res = array();
						$coords = preg_match('/^\s*(\d+);(\d+)\|(\d+)\|(\d+)\s*$/',$answer['hotspot_coord'],$res);
						$coords = $res[1].','.$res[2].','.((int)$res[1]+(int)$res[3]).",".((int)$res[2]+(int)$res[4]);
	        			break;
	        		case 'circle':
	        			$type = 'circle';
			 			$res = array();
						$coords = preg_match('/^\s*(\d+);(\d+)\|(\d+)\|(\d+)\s*$/',$answer['hotspot_coord'],$res);
						$coords = $res[1].','.$res[2].','.sqrt(pow(($res[1]-$res[3]),2)+pow(($res[2]-$res[4])));
	        			break;
	        		case 'poly':
	        			$type = 'poly';
						$coords = str_replace(array(';','|'),array(',',','),$answer['hotspot_coord']);
	        			break;
	        		 case 'delineation' :
	        			$type = 'delineation';
						$coords = str_replace(array(';','|'),array(',',','),$answer['hotspot_coord']);
	        			break;
	        	}
	            $text .= '        <hotspotChoice shape="'.$type.'" coords="'.$coords.'" identifier="'.$key.'"/>'."\n";
	        }
        }
        $text .= '      </graphicOrderInteraction>'."\n";
        $out = $text;

        return $out;
    }

    /**
     *
     */
    public function imsExportResponsesDeclaration($questionIdent)
    {
		$this->answerList = $this->getAnswersList(true);
		$this->gradeList = $this->getGradesList();
        $out = '';
        $out .= '  <responseDeclaration identifier="hotspot_'.$questionIdent.'" cardinality="ordered" baseType="identifier">' . "\n";
        $out .= '    <correctResponse>'. "\n";

		if (is_array($this->answerList)) {
	        foreach ($this->answerList as $answerKey=>$answer)  {
	        	$answerKey = $answer['id'];
	        	$answer = $answer['answer'];
	            $out .= '<value><![CDATA['.formatExerciseQtiTitle($answerKey).']]></value>';
	        }
		}
        $out .= '    </correctResponse>'. "\n";
        $out .= '  </responseDeclaration>'. "\n";

       return $out;
    }
}

/**
 * Class
 * @package chamilo.exercise
 */
class ImsAnswerFree extends Answer
{
    /**
     * TODO implement
     * Export the question part as a matrix-choice, with only one possible answer per line.
     */
    public function imsExportResponses($questionIdent, $questionStatment, $questionDesc='', $questionMedia='')
	{
		return '';
	}
    /**
     *
     */
    public function imsExportResponsesDeclaration($questionIdent)
    {
    	return '';
    }
}
