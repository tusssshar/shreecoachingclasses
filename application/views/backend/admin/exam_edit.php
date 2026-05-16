<?php
$question_id = $param2;
$question = $this->db->get_where('question', array('question_id' => $question_id))->row_array();
$options  = $this->db->order_by('label', 'asc')
    ->get_where('answer', array('question_id' => $question_id))->result_array();
if (!$question) {
    echo '<div class="alert alert-danger">Question not found.</div>';
    return;
}
$session_url = $question['session'] === '' ? '%null' : $question['session'];
$action = base_url() . 'index.php?admin/exam_view/'
        . $question['class_id'] . '/' . $question['subject_id'] . '/'
        . $question['duration'] . '/' . $question['date'] . '/'
        . $session_url . '/save/' . $question_id;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><i class="entypo-pencil"></i> Edit Question</div>
            </div>
            <div class="panel-body">
                <?php echo form_open($action, array('class' => 'form-horizontal form-groups-bordered validate', 'target' => '_top')); ?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Question</label>
                        <div class="col-sm-9">
                            <textarea name="question" class="form-control" rows="3"><?php echo htmlspecialchars($question['question']); ?></textarea>
                        </div>
                    </div>

                    <?php foreach ($options as $opt): ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Option <?php echo htmlspecialchars($opt['label']); ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="answers[]" class="form-control" value="<?php echo htmlspecialchars($opt['content']); ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Correct Answer</label>
                        <div class="col-sm-3">
                            <select name="correct_answers" class="form-control">
                                <?php foreach ($options as $opt): ?>
                                    <option value="<?php echo htmlspecialchars($opt['label']); ?>"
                                        <?php echo ($question['correct_answers'] === $opt['label']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($opt['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Marks</label>
                        <div class="col-sm-3">
                            <input type="number" min="0" step="1" name="marks" class="form-control"
                                   value="<?php echo isset($question['marks']) ? (int)$question['marks'] : 1; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">Save Question</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
