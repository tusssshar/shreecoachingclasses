<hr>
<?php $editing = isset($edit_language); ?>
<div class="panel panel-gradient">

    <div class="panel-heading">
        <div class="panel-title">
            <?php echo get_phrase('language_information_page'); ?>
        </div>
    </div>

    <div class="table-responsive">

        <!------CONTROL TABS START------>
        <ul class="nav nav-tabs bordered">
            <?php if ($editing): ?>
            <li class="active">
                <a href="#edit" data-toggle="tab"><i class="entypo-pencil"></i>
                    <?php echo get_phrase('edit_phrase'); ?>: <?php echo html_escape(sms_language_label($edit_language)); ?>
                </a></li>
            <?php endif; ?>
            <li class="<?php if (!$editing) echo 'active'; ?>">
                <a href="#list" data-toggle="tab"><i class="entypo-menu"></i>
                    <?php echo get_phrase('language_list'); ?>
                </a></li>
            <li>
                <a href="#add" data-toggle="tab"><i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('add_phrase'); ?>
                </a></li>
            <li>
                <a href="#add_lang" data-toggle="tab"><i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('add_language'); ?>
                </a></li>
        </ul>
        <!------CONTROL TABS END------>

        <div class="tab-content">

            <!----PHRASE EDITING TAB STARTS-->
            <?php if ($editing): ?>
            <div class="tab-pane active" id="edit" style="padding: 10px">

                <?php echo form_open(base_url() . 'index.php?admin/manage_language/search_phrase/' . $edit_language, array('class' => 'form-inline', 'style' => 'margin-bottom:12px;')); ?>
                    <input type="text" name="q" class="form-control" style="min-width:260px;"
                           value="<?php echo html_escape($search); ?>" placeholder="<?php echo get_phrase('search_phrase'); ?>">
                    <select name="filter" class="form-control">
                        <option value="all" <?php if ($filter == 'all') echo 'selected'; ?>><?php echo get_phrase('all_phrases'); ?></option>
                        <option value="missing" <?php if ($filter == 'missing') echo 'selected'; ?>><?php echo get_phrase('untranslated_only'); ?></option>
                    </select>
                    <button type="submit" class="btn btn-info"><i class="entypo-search"></i> <?php echo get_phrase('search'); ?></button>
                    <?php if ($search !== '' || $filter !== 'all'): ?>
                        <a href="<?php echo $editor_url($edit_language, 'all', 1, ''); ?>" class="btn btn-default"><?php echo get_phrase('clear'); ?></a>
                    <?php endif; ?>
                    <span class="text-muted" style="margin-left:10px;"><?php echo $total; ?> <?php echo get_phrase('phrases'); ?></span>
                <?php echo form_close(); ?>

                <?php echo form_open(base_url() . 'index.php?admin/manage_language/update_phrase/' . $edit_language, array('id' => 'phrase_form')); ?>
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <input type="hidden" name="q" value="<?php echo html_escape($search); ?>">

                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th style="width:30%"><?php echo get_phrase('english'); ?></th>
                                <th><?php echo html_escape(sms_language_label($edit_language)); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($phrases)): ?>
                                <tr><td colspan="2" class="text-center text-muted"><?php echo get_phrase('no_phrases_found'); ?></td></tr>
                            <?php endif; ?>
                            <?php foreach ($phrases as $row): ?>
                            <tr>
                                <td>
                                    <?php echo html_escape($row['english'] !== '' ? $row['english'] : sms_humanize_phrase($row['phrase'])); ?>
                                    <br><small class="text-muted"><?php echo html_escape($row['phrase']); ?></small>
                                </td>
                                <td>
                                    <input type="text" class="form-control phrase-input" name="phrase[<?php echo (int)$row['phrase_id']; ?>]"
                                           value="<?php echo html_escape($row['translation']); ?>"
                                           data-original="<?php echo html_escape($row['translation']); ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_changes'); ?></button>
                            <span id="phrase_changed" class="text-warning" style="margin-left:10px;"></span>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?php if ($pages > 1): ?>
                            <ul class="pagination" style="margin:0;">
                                <li class="<?php if ($page <= 1) echo 'disabled'; ?>">
                                    <a href="<?php echo $page > 1 ? $editor_url($edit_language, $filter, $page - 1, $search) : '#'; ?>">&laquo;</a></li>
                                <?php for ($p = max(1, $page - 3); $p <= min($pages, $page + 3); $p++): ?>
                                    <li class="<?php if ($p == $page) echo 'active'; ?>">
                                        <a href="<?php echo $editor_url($edit_language, $filter, $p, $search); ?>"><?php echo $p; ?></a></li>
                                <?php endfor; ?>
                                <li class="<?php if ($page >= $pages) echo 'disabled'; ?>">
                                    <a href="<?php echo $page < $pages ? $editor_url($edit_language, $filter, $page + 1, $search) : '#'; ?>">&raquo;</a></li>
                            </ul>
                            <div class="text-muted"><?php echo get_phrase('page'); ?> <?php echo $page; ?> / <?php echo $pages; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>

                <script>
                (function () {
                    var form = document.getElementById('phrase_form'), dirty = false;
                    var inputs = form.querySelectorAll('.phrase-input');
                    function refresh() {
                        var n = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            var changed = inputs[i].value !== inputs[i].getAttribute('data-original');
                            inputs[i].style.background = changed ? '#fff8e1' : '';
                            if (changed) n++;
                        }
                        dirty = n > 0;
                        document.getElementById('phrase_changed').textContent = n ? n + ' <?php echo addslashes(get_phrase('unsaved_changes')); ?>' : '';
                    }
                    for (var i = 0; i < inputs.length; i++) inputs[i].addEventListener('input', refresh);
                    form.addEventListener('submit', function () {
                        dirty = false;
                        // Only send edited phrases.
                        for (var i = 0; i < inputs.length; i++)
                            if (inputs[i].value === inputs[i].getAttribute('data-original')) inputs[i].disabled = true;
                    });
                    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
                })();
                </script>
            </div>
            <?php endif; ?>
            <!----PHRASE EDITING TAB ENDS-->

            <!----TABLE LISTING STARTS-->
            <div class="tab-pane <?php if (!$editing) echo 'active'; ?>" id="list">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo get_phrase('language'); ?></th>
                            <th style="width:40%"><?php echo get_phrase('translated'); ?></th>
                            <th><?php echo get_phrase('option'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($languages as $field):
                            $done = isset($translated[$field]) ? (int)$translated[$field] : 0;
                            $pct  = $total_phrases ? (int)floor($done * 100 / $total_phrases) : 0;
                        ?>
                        <tr>
                            <td>
                                <?php echo html_escape(sms_language_label($field)); ?>
                                <?php if ($field == $current_language): ?>
                                    <span class="label label-success"><?php echo get_phrase('current'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="progress" style="margin-bottom:4px;">
                                    <div class="progress-bar <?php echo $pct == 100 ? 'progress-bar-success' : 'progress-bar-warning'; ?>" style="width: <?php echo $pct; ?>%"></div>
                                </div>
                                <small><?php echo $done; ?> / <?php echo $total_phrases; ?> (<?php echo $pct; ?>%)</small>
                            </td>
                            <td>
                                <a href="<?php echo base_url(); ?>index.php?admin/manage_language/edit_phrase/<?php echo $field; ?>"
                                   class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-pencil"></i>
                                    <?php echo get_phrase('edit_phrase'); ?>
                                </a>
                                <?php if ($done < $total_phrases): ?>
                                <a href="<?php echo base_url(); ?>index.php?admin/manage_language/edit_phrase/<?php echo $field; ?>/missing"
                                   class="btn btn-warning btn-sm"><?php echo get_phrase('untranslated'); ?> (<?php echo $total_phrases - $done; ?>)</a>
                                <?php endif; ?>
                                <?php if ($field != 'english' && $field != $current_language): ?>
                                    <?php echo form_open(base_url() . 'index.php?admin/manage_language/delete_language/' . $field, array('style' => 'display:inline',
                                        'onsubmit' => "return confirm('" . addslashes(get_phrase('delete_language')) . ': ' . addslashes(sms_language_label($field)) . "? " . addslashes(get_phrase('this_cannot_be_undone')) . "');")); ?>
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon icon-left">
                                            <i class="entypo-trash"></i>
                                            <?php echo get_phrase('delete'); ?>
                                        </button>
                                    <?php echo form_close(); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted" style="padding:0 10px;">
                    <?php echo get_phrase('choose_the_system_language_in'); ?>
                    <a href="<?php echo base_url(); ?>index.php?admin/system_settings"><?php echo get_phrase('system_settings'); ?></a>.
                </p>
            </div>
            <!----TABLE LISTING ENDS--->

            <!----PHRASE CREATION FORM STARTS---->
            <div class="tab-pane box" id="add" style="padding: 5px">
                <div class="box-content">
                    <?php echo form_open(base_url() . 'index.php?admin/manage_language/add_phrase/', array('class' => 'form-horizontal form-groups-bordered validate')); ?>
                        <div class="padded">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('phrase'); ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="phrase" placeholder="e.g. fee_receipt" data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="fa fa-pencil"></i><?php echo get_phrase('add_phrase'); ?></button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!----PHRASE CREATION FORM ENDS--->

            <!----ADD NEW LANGUAGE---->
            <div class="tab-pane box" id="add_lang" style="padding: 5px">
                <div class="box-content">
                    <?php echo form_open(base_url() . 'index.php?admin/manage_language/add_language/', array('class' => 'form-horizontal form-groups-bordered validate')); ?>
                        <div class="padded">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('language'); ?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="language" placeholder="e.g. telugu" pattern="[A-Za-z]{2,30}"
                                           title="<?php echo get_phrase('language_name_letters_only'); ?>" data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="fa fa-pencil"></i><?php echo get_phrase('add_language'); ?></button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!----LANGUAGE ADDING FORM ENDS-->

        </div>
    </div>
</div>
