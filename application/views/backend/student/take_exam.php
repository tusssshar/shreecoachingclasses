<hr>
<div class="row">
    <div class="col-md-9">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><?php echo html_escape($exam['title']); ?> &middot; <?php echo html_escape($exam['subject_name']); ?></div>
            </div>
            <div class="panel-body">
                <?php if ($exam['instructions']): ?>
                    <div class="alert alert-info" style="margin-bottom:14px;"><?php echo nl2br(html_escape($exam['instructions'])); ?></div>
                <?php endif; ?>

                <?php echo form_open(base_url() . 'index.php?student/submit_exam/' . $exam['exam_id'], array('id' => 'exam_form')); ?>
                <?php foreach ($questions as $i => $q): $qid = (int)$q['question_id']; $mine = $saved[$qid] ?? ''; ?>
                    <div class="question-block" id="qb<?php echo $qid; ?>" data-qid="<?php echo $qid; ?>" style="border:1px solid #e3e7ee;border-radius:4px;padding:12px 14px;margin-bottom:12px;">
                        <div style="margin-bottom:8px;">
                            <strong><?php echo get_phrase('question'); ?> <?php echo $i + 1; ?>.</strong>
                            <span class="label label-default pull-right"><?php echo (int)$q['marks']; ?> <?php echo (int)$q['marks'] === 1 ? get_phrase('mark') : get_phrase('marks'); ?></span>
                            <div style="margin-top:4px;font-size:14px;"><?php echo nl2br(html_escape($q['question'])); ?></div>
                        </div>
                        <?php foreach ($q['options'] as $label => $content): if (trim($content) === '') continue; ?>
                            <label style="display:block;font-weight:normal;padding:6px 8px;border-radius:3px;cursor:pointer;" class="opt">
                                <input type="radio" name="answer[<?php echo $qid; ?>]" value="<?php echo $label; ?>" <?php if ($mine === $label) echo 'checked'; ?>>
                                <strong><?php echo $label; ?>.</strong> <?php echo html_escape($content); ?>
                            </label>
                        <?php endforeach; ?>
                        <a href="#" class="clear-answer small text-muted" style="<?php echo $mine === '' ? 'display:none;' : ''; ?>"><?php echo get_phrase('clear_answer'); ?></a>
                        <span class="save-state small text-muted pull-right"></span>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-success btn-lg"><i class="entypo-check"></i> <?php echo get_phrase('submit_exam'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-default" style="position:sticky;top:10px;">
            <div class="panel-body text-center">
                <div class="text-muted"><?php echo get_phrase('time_left'); ?></div>
                <div id="timer" style="font-size:34px;font-weight:bold;font-family:monospace;">--:--</div>
                <div class="text-muted" style="margin-top:6px;"><span id="answered_count">0</span> / <?php echo count($questions); ?> <?php echo get_phrase('answered'); ?></div>
                <div id="palette" style="margin-top:10px;display:flex;flex-wrap:wrap;gap:4px;justify-content:center;">
                    <?php foreach ($questions as $i => $q): ?>
                        <a href="#qb<?php echo $q['question_id']; ?>" class="btn btn-default btn-xs pal" data-qid="<?php echo $q['question_id']; ?>" style="width:34px;"><?php echo $i + 1; ?></a>
                    <?php endforeach; ?>
                </div>
                <div id="net_warn" class="text-danger small" style="display:none;margin-top:8px;"><?php echo get_phrase('connection_problem_answers_will_be_sent_on_submit'); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var secondsLeft = <?php echo (int)$seconds_left; ?>, deadline = Date.now() + secondsLeft * 1000;
    var form = document.getElementById('exam_form'), submitting = false, total = <?php echo count($questions); ?>;
    var saveUrl = '<?php echo base_url(); ?>index.php?student/save_answer/<?php echo (int)$exam['exam_id']; ?>';
    var msgSaved = '<?php echo addslashes(get_phrase('saved')); ?>', msgSaving = '<?php echo addslashes(get_phrase('saving')); ?>';

    function refresh() {
        var answered = 0;
        document.querySelectorAll('.question-block').forEach(function (b) {
            var on = !!b.querySelector('input[type=radio]:checked');
            if (on) answered++;
            var p = document.querySelector('.pal[data-qid="' + b.getAttribute('data-qid') + '"]');
            p.className = 'btn btn-xs pal ' + (on ? 'btn-success' : 'btn-default');
            b.querySelector('.clear-answer').style.display = on ? '' : 'none';
            b.querySelectorAll('.opt').forEach(function (l) { l.style.background = l.querySelector('input').checked ? '#e8f4ea' : ''; });
        });
        document.getElementById('answered_count').textContent = answered;
        return answered;
    }

    function save(qid, value, block) {
        var state = block.querySelector('.save-state');
        state.textContent = msgSaving;
        var body = 'question_id=' + encodeURIComponent(qid) + '&answer=' + encodeURIComponent(value);
        fetch(saveUrl, { method: 'POST', credentials: 'same-origin', body: body,
                         headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok) {
                    state.textContent = msgSaved; document.getElementById('net_warn').style.display = 'none';
                    deadline = Date.now() + d.seconds_left * 1000;            // keep the clock in step with the server
                } else if (d.error === 'time_up' || d.error === 'not_in_progress') { finish(); }
                else { state.textContent = ''; document.getElementById('net_warn').style.display = ''; }
            })
            .catch(function () { state.textContent = ''; document.getElementById('net_warn').style.display = ''; });
    }

    form.addEventListener('change', function (e) {
        if (e.target.type !== 'radio') return;
        var block = e.target.closest('.question-block');
        save(block.getAttribute('data-qid'), e.target.value, block);
        refresh();
    });
    document.querySelectorAll('.clear-answer').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var block = a.closest('.question-block');
            block.querySelectorAll('input[type=radio]').forEach(function (r) { r.checked = false; });
            save(block.getAttribute('data-qid'), '', block);
            refresh();
        });
    });

    function finish() {
        if (submitting) return;
        submitting = true;
        form.submit();
    }
    form.addEventListener('submit', function (e) {
        if (submitting) return;
        var left = total - refresh();
        var msg = left > 0 ? left + ' <?php echo addslashes(get_phrase('questions_are_unanswered')); ?>. ' : '';
        if (!confirm(msg + '<?php echo addslashes(get_phrase('submit_exam_confirm')); ?>')) { e.preventDefault(); return; }
        submitting = true;
    });
    window.addEventListener('beforeunload', function (e) { if (!submitting) { e.preventDefault(); e.returnValue = ''; } });

    function tick() {
        var s = Math.max(0, Math.round((deadline - Date.now()) / 1000));
        var m = Math.floor(s / 60), r = s % 60, el = document.getElementById('timer');
        el.textContent = (m < 10 ? '0' : '') + m + ':' + (r < 10 ? '0' : '') + r;
        el.style.color = s <= 60 ? '#d9534f' : (s <= 300 ? '#f0ad4e' : '');
        if (s <= 0) { finish(); return; }
        setTimeout(tick, 1000);
    }
    refresh();
    tick();
})();
</script>
