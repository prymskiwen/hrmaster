<?php

const SHAPE_DRAW_STROKE      = 0;

if (!class_exists('db')) {
    require('db.class.php');
}
class audit {

    var $post = array();
    var $pdf = null;
    var $maxHeadingLength = 560;
    var $font = array();

    function __construct($post=array()) {
        $this->post = $post;
    }

    private function _describeTable($table, $db=false) {
        $data   = array();
        $a      = new db(false);
        $a->select(false,false, "DESCRIBE $table", array());
        while($a->getRow()) {
            array_push($data, $a->row);
        }

        return $data;
    }

    public function getChecklists($returnJson=true) {
        $db = new db('auditchecklist');

        $sql = "SELECT *, 'Template' AS checklistType  
                  FROM auditchecklist
                 WHERE type = :type 
                   AND deleted = :notdel
                UNION
                SELECT *, 'Favourite' AS checklistType 
                  FROM auditchecklist
                 WHERE type <> :type 
                   AND deleted = :notdel
                   AND account_id = :uid
              ORDER BY checklistType ASC, date_created DESC";

        $account = (isset($this->post->userData->account_id)) ? $this->post->userData->account_id : 0;
        $db->select(false, false, $sql, array('type' => 'T', 'notdel' => 0, 'uid' => $account));

        $data = array();
        while($db->getRow()) {
            //$db->row['isAdmin'] = ($this->post->userData->member_account == 'S') ? 1 : 0;
            array_push($data, $db->row);
        }

       return ($returnJson) ? json_encode(array('checklists' => $data)) : $data;
    }

    private function getCheckListById($id) {
        $data = array();
        $c = new db('auditchecklist');
        $c->select('id = :id', false, false, array('id' => $id));
        if ($c->numRows > 0) {
            $c->getRow();
            $data = $c->row;
        }
        return $data;
    }

    private function copyChecklist($id) {
        $c = new db('auditchecklist');
        $c->select('id = :id', false, false, array('id' => $id));
        if ($c->numRows === 0) {
            return false;
        }
        $c->getRow();
        $data = array();
        $data['account_id'] = $this->post->userData->account_id;
        $data['name'] = $c->name;
        $data['type'] = 'F';
        $data['created_by'] = $this->post->userData->id;
        $c->insert($data);
        return $c->lastInsertId;
    }

    public function duplicateChecklist() {
        $newChecklistID = $this->copyChecklist($this->post->checklist->id);
        if ($newChecklistID === false || !$newChecklistID) {
            return;
        }

        $db = new db('auditchecklist_item');
        $i = new db('auditchecklist_item');
        $new = new db('auditchecklist_item');
        $new->bindParams = true;

        // Get all parent items first
        $db->select('checklist_id = :cid AND deleted = :notdel AND parent_id = :pid', 'display_order ASC', false, array('cid' => $this->post->checklist->id, 'notdel' => 0, 'pid' => 0));
        while ($db->getRow()) {
            $items = array();
            $items['name'] = $db->name;
            $items['parent_id'] = 0;
            $items['checklist_id'] = $newChecklistID;
            $items['display_order'] = $db->display_order;
            $items['added_by'] = $this->post->userData->id;
            $new->insert($items);
            $iinsertId = $new->lastInsertId;

            $i->select('parent_id = :pid AND deleted = :notdel', 'display_order ASC', false, array('pid' => $db->id, 'notdel' => 0));
            while ($i->getRow()) {
                $items = array();
                $items['name'] = $i->name;
                $items['parent_id'] = $iinsertId;
                $items['checklist_id'] = $newChecklistID;
                $items['display_order'] = $i->display_order;
                $items['added_by'] = $this->post->userData->id;
                $new->insert($items);
            }
        }

        $data = array();
        $data['checklists'] = $this->getChecklists(false);

        return json_encode($data);
    }

    private function getChecklistSubItems($parent_id) {
        $items = array();
        $i = new db('auditchecklist_item');
        $i->select('parent_id = :pid AND deleted = :notdel', 'display_order ASC', false, array('pid' => $parent_id, 'notdel' => 0));
        while ($i->getRow()) {
            array_push($items, $i->row);
        }
        return $items;
    }

    public function getChecklist($id, $returnJson=true) {
        $data = array();
        $db = new db('auditchecklist_item');
        $db->select('checklist_id = :cid AND deleted = :notdel AND parent_id = :pid', 'display_order ASC', false, array('cid' => $id, 'notdel' => 0, 'pid' => 0));

        while ($db->getRow()) {
            $row = $db->row;
            $row['subitems'] = $this->getChecklistSubItems($db->id);
            array_push($data, $row);
        }

        $returnData = array('checklist' => $data, 'name' => $this->getCheckListById($id));

        return ($returnJson) ? json_encode($returnData) : $returnData;

    }

    public function deleteChecklist() {
        $db = new db('auditchecklist');
        $params = array('deleted' => 1, 'deleted_by' => 1, 'date_deleted' => 1);
        $data = array('deleted' => 1, 'deleted_by' => $this->post->userData->id, 'date_deleted' => date('Y-m-d H:i:s'), 'id' => $this->post->checklist->id);
        $db->update($params, 'id = :id', 1, $data);

        $data = array();
        $data['checklists'] = $this->getChecklists(false);

        return json_encode($data);
    }

    public function updateChecklist() {

        $db = new db('auditchecklist');
        $db->update(array('name' => 1), 'id = :iid', 1, array('name' => $this->post->name, 'iid' => $this->post->checklist->id));

        $data = $this->getChecklist($this->post->checklist->id, false);
        $data['checklists'] = $this->getChecklists(false);

        return json_encode($data);
    }

    public function updateChecklistItem() {
        $db = new db('auditchecklist_item');
        $db->update(array('name' => 1), 'id = :iid', 1, array('name' => $this->post->name, 'iid' => $this->post->checklist->id));

        return $this->getChecklist($this->post->checklist->checklist_id);
    }

    public function newChecklist() {
        $data = array();
        $data['name'] = $this->post->checklist;
        $data['type'] = 'T';
        $data['account_id'] = $this->post->userData->account_id;
        $data['created_by'] = $this->post->userData->id;
        $db = new db('auditchecklist');
        $db->insert($data);

        $data = array();
        $data['checklists'] = $this->getChecklists(false);
        $data['checklist'] = $this->getChecklist($db->lastInsertId, false);
        return json_encode($data);
    }

    private function getMaxDisplayOrder($checklist_id, $parent) {
        $db = new db('auditchecklist_item');
        $db->select('checklist_id = :cid AND parent_id = :p', 'display_order ASC', false, array('cid' => $checklist_id, 'p' => $parent));
        $max = 0;
        while($db->getRow()) {
            $max = ($db->display_order > $max) ? $db->display_order : $max;
        }
        return ($max == 0) ? $db->numRows : $max + 1;
    }

    public function newChecklistGroup() {
        $db = new db('auditchecklist_item');

        $data = array();
        $data['name'] = $this->post->name;
        $data['parent_id'] = 0;
        $data['checklist_id'] = $this->post->checklist_id;
        $data['display_order'] = $this->getMaxDisplayOrder($this->post->checklist_id, 0);
        $data['added_by'] = $this->post->userData->id;
        $db->insert($data);

        $data = $this->getChecklist($this->post->checklist_id, false);
        return json_encode($data);
    }

    public function newChecklistGroupItem() {
        $db = new db('auditchecklist_item');

        $data = array();
        $data['name'] = $this->post->name;
        $data['parent_id'] = $this->post->group->id;
        $data['checklist_id'] = $this->post->group->checklist_id;
        $data['display_order'] = $this->getMaxDisplayOrder($this->post->group->checklist_id, $this->post->group->id);
        $data['added_by'] = $this->post->userData->id;
        $db->insert($data);

        $data = $this->getChecklist($this->post->group->checklist_id, false);
        return json_encode($data);
    }

    private function updateDisplayOrder($item_id, $order) {
        $i = new db('auditchecklist_item');
        $i->update(array('display_order' => 1), 'id = :id', 1, array('display_order' => $order, 'id' => $item_id));
    }

    // Direction: 0-Up : 1-Down
    public function reorderChecklistItem() {

        $direction = ($this->post->direction == 1) ? 'ASC' : 'DESC';
        $operator = ($this->post->direction == 1) ? '>' : '<';

        $db = new db('auditchecklist_item');
        $currDisplayOrder = $this->post->item->display_order;
        $params = array('cid' => $this->post->item->checklist_id, 'pid' => $this->post->item->parent_id, 'notdel' => 0, 'order' => $currDisplayOrder);
        $db->select('checklist_id = :cid AND parent_id = :pid AND deleted = :notdel AND display_order '.$operator.' :order', 'display_order '.$direction, false, $params);
        if ($db->numRows === 0) {
            $data = $this->getChecklist($this->post->item->checklist_id, false);
            return json_encode($data);
        }
        $db->getRow();
        $this->updateDisplayOrder($this->post->item->id, $db->display_order);
        $this->updateDisplayOrder($db->id, $currDisplayOrder);

        $data = $this->getChecklist($this->post->item->checklist_id, false);
        return json_encode($data);
    }

    public function deleteChecklistItem() {
        $i = new db('auditchecklist_item');
        $data = array('deleted' => 1, 'deleted_by' => $this->post->userData->id, 'date_deleted' => date('Y-m-d H:i:s'));
        $params = $data;
        $params['id'] = $this->post->item->id;
        $i->update($data, 'id = :id', 1, $params);

        $data = $this->getChecklist($this->post->item->checklist_id, false);
        return json_encode($data);
    }

    private function getActionChecklistSubItems($parent_id) {
        $items = array();
        $i = new db('auditchecklist_action_item');

        $sql = "SELECT *, item_id AS IID, checklist_id AS CID,
                    (SELECT name FROM auditchecklist_item WHERE id = IID) AS Name,
                    (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action_item
                WHERE parent_id = :pid
             ORDER BY display_order ASC";


        $i->select(false, false, $sql, array('pid' => $parent_id));
        while ($i->getRow()) {
            $i->row['Name'] = (trim($i->Name) === '') ? $i->name : $i->row['Name'];
            array_push($items, $i->row);
        }
        return $items;
    }

    private function _getActionLists() {
        $db = new db('auditchecklist_action');

        $sql = "SELECT *, checklist_id AS CID, IF(complete = 0, 'In progress','Complete') AS Status,
                        DATE_FORMAT(date_created, '%d/%m/%Y') as DateCreated,
                        DATE_FORMAT(completed_date, '%d/%m/%Y') as DateCompleted,
                        DATE_FORMAT(completed_date, '%Y-%m-%d') as completed_date,
                        (SELECT name FROM auditchecklist WHERE id = CID) as ChecklistName
                  FROM auditchecklist_action
                 WHERE deleted = :notdel
                   AND archived = :notarchive
                   AND account_id = :uid
              ORDER BY complete ASC, date_created ASC";

        $db->select(false, false, $sql, array('notdel' => 0, 'uid' => $this->post->userData->account_id, 'notarchive' => 0));

        $data = array();
        while($db->getRow()) {
            //$db->row['isAdmin'] = ($this->post->userData->member_account == 'S') ? 1 : 0;
            array_push($data, $db->row);
        }
        return $data;
    }

    public function getEmployees($aid=false) {
        $db = new db('user');
        $sql = "SELECT *, CONCAT(firstname,' ', lastname) as name
                  FROM user
                 WHERE deleted = :del AND account_id = :uid
                 ORDER BY lastname, firstname";
        $account = ($aid) ? $aid : $this->post->userData->account_id;
        $db->select(false, false, $sql, array('uid' => $account, 'del' => 0));
        $data = array();
        while($db->getRow()) {
            array_push($data, $db->row);
        }
        return $data;
    }

    public function getActionChecklists() {
        $account_id = $this->post->userData->account_id;
        $data = $this->_getActionLists();
        $emps = $this->getEmployees($account_id);
        $checklists = $this->getChecklists(false);
        return json_encode(array('actionlists' => $data, 'checklists' => $checklists, 'employees' => $emps));
    }

    public function getActionChecklist($aid) {
        $returnData = array();
        $data = array();
        $a = new db('auditchecklist_action');
        $sql = "SELECT *, checklist_id AS CID,
                        (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action
                 WHERE id = :aid";
        $a->select(false, false, $sql, array('aid' => $aid));
        $a->getRow();
        $returnData['checklist'] = $a->row;
        $db = new db('auditchecklist_action_item');
        $sql = "SELECT *, item_id AS IID, checklist_id AS CID,
                    (SELECT name FROM auditchecklist_item WHERE id = IID) AS Name,
                    (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action_item
                WHERE action_id = :aid 
                  AND parent_id = :pid
             ORDER BY display_order ASC";
        $db->select(false, false, $sql, array('aid' => $aid, 'pid' => 0));
        while ($db->getRow()) {
            $row = $db->row;
            $row['subitems'] = $this->getActionChecklistSubItems($db->id);
            array_push($data, $row);
        }

        $returnData['items'] = $data;
        return $returnData;

    }
    private function getReportActionChecklistSubItems($parent_id) {
        $items = array();
        $i = new db('auditchecklist_action_item');

        $sql = "SELECT *, item_id AS IID, checklist_id AS CID, IF(is_ok=1, 'Yes','No') AS IsOK, allocated_to AS AID,completed_by AS CompleteID,
                    (SELECT name FROM auditchecklist_item WHERE id = IID) AS Name,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = AID) AS AllocatedTo,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = CompleteID) AS CompletedBy,
                    DATE_FORMAT(proposed_completion_date, '%d/%m/%Y') as ProposedCompletionDate,
                    DATE_FORMAT(date_of_completion, '%d/%m/%Y') as DateOfCompletion,
                    (SELECT name FROM auditchecklist_item WHERE id = IID) AS Name,
                    (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action_item
                WHERE parent_id = :pid
             ORDER BY display_order ASC";

        $i->select(false, false, $sql, array('pid' => $parent_id));
        while ($i->getRow()) {
            $i->row['Name'] = (trim($i->Name) === '') ? $i->name : $i->row['Name'];
            array_push($items, $i->row);
        }
        return $items;
    }
    public function getReportActionChecklist($cid) {
        $returnData = array();
        $data = array();
        $a = new db('auditchecklist_action');
        $sql = "SELECT *, checklist_id AS CID, IF(other_issues IS NULL OR other_issues = '', 'None', other_issues) AS OtherIssues,
                        DATE_FORMAT(completed_date, '%d/%m/%Y') as CompletedDate,
                        IF(next_audit_date IS NULL OR next_audit_date = '0000-00-00', 'Not set', DATE_FORMAT(next_audit_date, '%d/%m/%Y')) as NextAuditDate,
                        (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action
                 WHERE id = :aid";
        $a->select(false, false, $sql, array('aid' => $cid));
        $a->getRow();
        $returnData['checklist'] = $a->row;

        $d = new stdClass();
        $d->userData = new stdClass();
        $d->userData->id = $a->user_id;

        $db = new db('auditchecklist_action_item');
        $sql = "SELECT *, item_id AS IID, checklist_id AS CID, IF(is_ok=1, 'Yes','No') AS IsOK, allocated_to AS AID,completed_by AS CompleteID,
                    (SELECT name FROM auditchecklist_item WHERE id = IID) AS Name,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = AID) AS AllocatedTo,
                    (SELECT CONCAT(firstname,' ',lastname) FROM user WHERE id = CompleteID) AS CompletedBy,
                    DATE_FORMAT(proposed_completion_date, '%d/%m/%Y') as ProposedCompletionDate,
                    DATE_FORMAT(date_of_completion, '%d/%m/%Y') as DateOfCompletion,
                    (SELECT name FROM auditchecklist WHERE id = CID) AS ChecklistName
                  FROM auditchecklist_action_item
                WHERE action_id = :aid 
                  AND parent_id = :pid
             ORDER BY display_order ASC";
        $db->select(false, false, $sql, array('aid' => $cid, 'pid' => 0));
        while ($db->getRow()) {
            $row = $db->row;
            $row['subitems'] = $this->getReportActionChecklistSubItems($db->id);
            array_push($data, $row);
        }

        $returnData['items'] = $data;

        return $returnData;
    }
    public function createAuditAction() {
        $a = new db('auditchecklist');
        $ai = new db('auditchecklist_item');
        $aisub = new db('auditchecklist_item');
        $aa = new db('auditchecklist_action');
        $aai = new db('auditchecklist_action_item');


        $a->select('id = :cid', false, false, array('cid' => $this->post->checklist_id));
        if ($a->numRows == 0) {
            return;  // Could not create audit
        }

        $a->getRow();
        $data = array();
        $data['checklist_id'] = $this->post->checklist_id;
        $data['account_id'] = $this->post->userData->account_id;
        $data['created_by'] = $this->post->userData->id;
        $data['start_date'] = date('Y-m-d');
        $aa->insert($data);
        $actionId = $aa->lastInsertId;

        $ai->select('checklist_id = :cid AND deleted = :notdel AND parent_id = :pid', 'display_order ASC', false, array('cid' => $this->post->checklist_id, 'notdel' => 0, 'pid' => 0));

        while ($ai->getRow()) {
            $data = array();
            $data['action_id'] = $actionId;
            $data['checklist_id'] = $this->post->checklist_id;
            $data['parent_id'] = $ai->parent_id;
            $data['account_id'] = $this->post->userData->account_id;
            $data['item_id'] = $ai->id;
            $data['display_order'] = $ai->display_order;
            $data['created_by'] = $this->post->userData->id;
            $aai->insert($data);
            $pid = $aai->lastInsertId;

            $aisub->select('deleted = :notdel AND parent_id = :pid', 'display_order ASC', false, array('notdel' => 0, 'pid' => $ai->id));
            while ($aisub->getRow()) {
                $data = array();
                $data['action_id'] = $actionId;
                $data['checklist_id'] = $this->post->checklist_id;
                $data['parent_id'] = $pid;
                $data['account_id'] = $this->post->userData->account_id;
                $data['item_id'] = $aisub->id;
                $data['display_order'] = $aisub->display_order;
                $data['created_by'] = $this->post->userData->id;
                $aai->insert($data);
            }

        }

        $data = $this->getActionChecklist($actionId);
        $data['actionlists'] = $this->_getActionLists();
        return json_encode($data);
    }

    public function saveAuditActionItem() {
        if (!isset($this->post->field) || !$this->post->field) {
            return;
        }

        $itemId = $this->post->item->id;
        $field = $this->post->field;

        if (in_array($field, array('date_of_completion','proposed_completion_date'))) {
            if (isset($this->post->obj->$itemId->$field)) {
                $this->post->obj->$itemId->$field = date('Y-m-d', strtotime($this->post->obj->$itemId->$field));
            } else {
                $this->post->obj->$itemId->$field = '';
            }
        }

        $data = array();
        $data[$field] = (isset($this->post->obj->$itemId->$field)) ? $this->post->obj->$itemId->$field : '';
        $data['last_updated_by'] = $this->post->userData->id;
        $params = $data;
        $params['iid'] = $itemId;
        $a = new db('auditchecklist_action_item');
        $a->bindParams = true;
        $a->update($data, 'id = :iid', 1, $params);
    }

    public function saveAuditActionResult() {
        $aa = new db('auditchecklist_action');
        $aa->bindParams = true;
        $data = array();
        $data['other_issues'] = (isset($this->post->data->other_issues)) ? $this->post->data->other_issues : '';
        $data['completed_by'] = (isset($this->post->data->completed_by)) ? $this->post->data->completed_by : '';
        $data['complete'] = $this->post->data->complete;
        $data['next_audit_date'] = (isset($this->post->data->next_audit_date)) ? date('Y-m-d', strtotime($this->post->data->next_audit_date)) : '';
        if ($this->post->data->complete == 1) {
            $data['completed_date'] = date('Y-m-d H:i:s');
        }

        $params = $data;
        $params['id'] = $this->post->item->id;

        $aa->update($data, 'id = :id', 1, $params);

        return $this->getActionChecklists();
    }
    public function archiveAuditActionChecklist() {
        $db = new db('auditchecklist_action');
        $params = array('archived' => 1, 'archived_by' => 1, 'date_archived' => 1);
        $data = array('archived' => 1, 'archived_by' => $this->post->userData->id, 'date_archived' => date('Y-m-d H:i:s'), 'id' => $this->post->item->id);
        $db->update($params, 'id = :id', 1, $data);

        return $this->getActionChecklists();
    }

    public function deleteAuditActionChecklist() {
        $db = new db('auditchecklist_action');
        $params = array('deleted' => 1, 'deleted_by' => 1, 'date_deleted' => 1);
        $data = array('deleted' => 1, 'deleted_by' => $this->post->userData->id, 'date_deleted' => date('Y-m-d H:i:s'), 'id' => $this->post->item->id);
        $db->update($params, 'id = :id', 1, $data);

        return $this->getActionChecklists();
    }
    private function _getTextWidth($text, $resource, $fontSize = null, $encoding = 'UTF-8') {

        if( $resource instanceof Zend_Pdf_Page ){
            $font = $resource->getFont();
            $fontSize = $resource->getFontSize();
        }elseif( $resource instanceof Zend_Pdf_Resource_Font ){
            $font = $resource;
            if( $fontSize === null ) throw new Exception('The fontsize is unknown');
        }

        if( !$font instanceof Zend_Pdf_Resource_Font ){
            throw new Exception('Invalid resource passed');
        }

        $drawingText = $text;//iconv ( '', $encoding, $text );
        $characters = array ();
        for($i = 0; $i < strlen ( $drawingText ); $i ++) {
            $characters [] = ord ( $drawingText [$i] );
        }
        $glyphs = $font->glyphNumbersForCharacters ( $characters );
        $widths = $font->widthsForGlyphs ( $glyphs );
        $textWidth = (array_sum ( $widths ) / $font->getUnitsPerEm ()) * $fontSize;
        return $textWidth;
    }

    public function bulkPrintChecklists() {
        $this->_printIncludes();
        $now = time();
        $save_filename  = '/home/hrmaster/public_html/assets/userdata/checklists/checklistgroup-'.$now.'.pdf';
        $http_filename = '//hrmaster.com.au/assets/userdata/checklists/checklistgroup-'.$now.'.pdf';

        // Create new PDF Document
        $this->pdf            = new Zend_Pdf();

        foreach($this->post->list as $key => $checklistId) {
            if (!$checklistId) {
                continue;
            }

            $checklist = $this->getCheckListById($checklistId);
            $checklistData = $this->getChecklist($checklistId, false);
            $page_no = 0;
            $page           = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
            $page_no++;

            $y          = 800;
            $pageDat = $this->setupPage($page, $font, $checklist, $page_no, $y);
            $page = $pageDat['page'];
            $y = $pageDat['y'];

            $y  = $y - 20;

            $page = $this->print_checklist_body($page, $checklistData, $font, $y, $page_no);

            $this->pdf->pages[]   = $page;

            if ($this->post->createActivity == 1) {
                $this->post->checklist_id = $checklistId;
                $this->createAuditAction();
            }

        }
        // Update the PDF document
        $this->pdf->save($save_filename, true);

        // Save document as a new file
        $this->pdf->save($save_filename);

        return json_encode(array('url' => $http_filename, 'checklist' => $checklist));
    }
    private function print_checklist_body($page, $checklistData, $font, $y, $page_no=1) {
        // Sub header
        foreach($checklistData['checklist'] as $id => $category) {
            if ($this->_getTextWidth(strtoupper($category['name']), $this->font['bold'] , 10) > $this->maxHeadingLength) {
                $arr = explode(' ', $category['name']);
                $str = '';

                $numLines = 0;
                foreach($arr as $k => $word) {
                    $testStr = ($str == '') ? $word : $str.' '.$word;

                    if ($this->_getTextWidth(strtoupper($testStr), $this->font['bold'] , 10) <= $this->maxHeadingLength) {
                        $str .= ($str == '') ? $word : ' '.$word;
                    } else {
                        $page->setFont($this->font['bold'] , 10)
                            ->setFillColor(Zend_Pdf_Color_Html::color('#DDDDDD'))
                            ->setLineColor(new Zend_Pdf_Color_Rgb(.8,.8,.8))
                            ->drawRectangle(10, $y, 590, $y+20, Zend_Pdf_Page::SHAPE_DRAW_FILL)
                            ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))
                            ->drawText(strtoupper($str), 15, $y+6);
                        $str = $word;
                        $testStr = '';
                        $numLines++;
                    }
                }

                if ($str) {
                    $y -= 14;
                    $page->setFont($this->font['bold'] , 10)
                        ->setFillColor(Zend_Pdf_Color_Html::color('#DDDDDD'))
                        ->setLineColor(new Zend_Pdf_Color_Rgb(.8,.8,.8))
                        ->drawRectangle(10, $y, 590, $y+20, Zend_Pdf_Page::SHAPE_DRAW_FILL)
                        ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))
                        ->drawText(strtoupper($str), 15, $y+6);
                    $str = '';
                    $numLines++;
                }

            } else {
                $page->setFont($this->font['bold'] , 10)
                    ->setFillColor(Zend_Pdf_Color_Html::color('#DDDDDD'))
                    ->setLineColor(new Zend_Pdf_Color_Rgb(.8,.8,.8))
                    ->drawRectangle(10, $y, 590, $y+20, Zend_Pdf_Page::SHAPE_DRAW_FILL)
                    ->setFillColor(Zend_Pdf_Color_Html::color('#000000'))
                    ->drawText(strtoupper($category['name']), 15, $y+6);
            }

            $y -= 14;

            if (count($category['subitems']) == 0) {
                continue;
            }

            foreach($category['subitems'] as $subid => $item) {
                $page->setFont($this->font['normal'], 10)
                     ->setFillColor(Zend_Pdf_Color_Html::color('#000000'));

                $str = '';
                $nArr = explode(' ', $item['name']);
                while (count($nArr) > 0) {
                    $segment = array_shift($nArr);
                    $len = $this->_getTextWidth($str, $this->font['normal'], 10) + $this->_getTextWidth($segment, $this->font['normal'], 10);

                    if ($len > 170) {
                        $page->drawText($str, 10, $y+1);
                        $y = $y - 10;
                        $str = '';
                    }
                    if ($str == '') {
                        $str = $segment;
                    } else {
                        $str .= ' '.$segment;
                    }
                }

                $page->drawText($str, 10, $y+1);
                $page->setLineColor(new Zend_Pdf_Color_Rgb(.6,.6,.6))
                     ->drawRectangle(186, $y, 196, $y+10,SHAPE_DRAW_STROKE)
                     ->drawRectangle(215, $y, 225, $y+10,SHAPE_DRAW_STROKE)
                     ->setLineColor(new Zend_Pdf_Color_Rgb(.8,.8,.8))
                     ->drawLine(10, $y-5, 590, $y-5);
                $y -= 20;
                if ($y < 20) {
                    $this->pdf->pages[]   = $page;
                    $page           = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
                    $page_no++;
                    $y = 800;

                    $page->setFont($this->font['normal'], 10)
                    ->drawText('Page '.$page_no, 540, $y+27);

                }
            }
            if ($y < 800) {
                $y -= 20;
                if ($y < 20) {
                   $this->pdf->pages[]   = $page;
                   $page           = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
                   $page_no++;
                   $y = 800;
                   $page->setFont($this->font['normal'], 10)->drawText('Page '.$page_no, 540, $y+27);
                }
            }

        }
        return $page;
    }

    private function setupPage($page, $font, $checklist, $page_no, $y) {
        $date           = date("d/m/Y");
        $page->setFont($this->font['normal'], 10)
             ->drawText($date, 20, 800)
             ->setFont($this->font['bold'] , 18)
             ->drawText($checklist['name'].' Checklist', PAGE_WIDTH_HALF - ($this->_getTextWidth($checklist['name'].' Checklist', $this->font['bold'] , 16) / 2), $y);

        $page->setFont($this->font['normal'], 10)
             ->drawText('Page '.$page_no, 540, $y);

        $y = $y - 60;
        $page->setFont($this->font['bold'] , 12)
             ->drawText('Do you have?', 10, $y)
             ->drawText('Yes', 180, $y)
             ->drawText('No', 210, $y)
             ->drawText('Corrective action', 240, $y)
             ->drawText('Proposed', 415, $y+30)
             ->drawText('completion', 410, $y+15)
             ->drawText('date', 425, $y)
             ->drawText('Allocated to', 500, $y);

        $y -= 10;
        $page->setLineWidth(2)
             ->setLineColor(new Zend_Pdf_Color_Rgb(0,0,0))
             ->drawLine(10, $y, 590, $y)
             ->setLineWidth(.5);

        return array('page' => $page, 'y' => $y);
    }

    private function _printIncludes() {
        // Page Dimensions for A4 - 595 wide by 842 high

        define('PAGE_WIDTH', 595);
        define('PAGE_HEIGHT', 842);
        define('PAGE_WIDTH_HALF', 297);
        define('PAGE_HEIGHT_HALF', 421);


        set_include_path('/home/hrmaster/public_html/assets/php' );

        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Style.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Cmyk.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Html.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/GrayScale.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Color/Rgb.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Page.php';
        require_once '/home/hrmaster/public_html/assets/php/Zend/Pdf/Font.php';

        $this->font['normal'] = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->font['bold']   = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
    }
    public function printChecklist($id) {
        $font = '';
        $this->_printIncludes();

        $checklist = $this->getCheckListById($id);
        $checklistData = $this->getChecklist($id, false);

        $save_filename  = '/home/hrmaster/public_html/assets/files/checklists/checklist-'.$id.'.pdf';
        $http_filename = '//hrmaster.com.au/assets/files/checklists/checklist-'.$id.'.pdf';

        $page_no        = 0;
        $date           = date("d/m/Y");

        // Create new PDF Document
        $this->pdf            = new Zend_Pdf();

        $page           = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $page_no++;

        $y          = 800;
        $pageDat = $this->setupPage($page, $font, $checklist, $page_no, $y);
        $page = $pageDat['page'];
        $y = $pageDat['y'];

        $y  = $y - 20;

        $page = $this->print_checklist_body($page, $checklistData, $font, $y, $page_no);

        $this->pdf->pages[]   = $page;

        // Update the PDF document
        $this->pdf->save($save_filename, true);

        // Save document as a new file
        $this->pdf->save($save_filename);

        unset($this->pdf);
        unset($page);

        return json_encode(array('url' => $http_filename, 'checklist' => $checklist));

    }
}

?>
