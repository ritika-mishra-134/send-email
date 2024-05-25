<?php
/**
 *
 */
// class Assessment {
//     /**
//      * Assessment id from {local_assessment}
//      */
//     private $_id;
//     private $_assessment;
//     private $_assessmentpartner;
//     /**
//      * @param int $id Assessmentid
//      *
//      * @return Assessment
//     */
//     function __construct($id) {
//         global $DB;
//         $this->_id = $id;
//         $this->_assessment = $DB->get_record(
//             'assessment',
//             array('id' => $this->_id)
//         );
//         $this->_assessmentpartner = $DB->get_record(
//             'assessment_partner',
//             array('id' => $this->_assessment->assessmentpartner)
//         );

//     }


// }