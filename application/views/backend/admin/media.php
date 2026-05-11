<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('flash_message'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left"><?php echo get_phrase('add_youtube_media'); ?></h4>
            </div>
            <div class="panel-body">
                <form action="<?php echo base_url(); ?>index.php?admin/media/create" method="post" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="title">Title <span class="text-danger">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" id="title" class="form-control" name="title" required placeholder="e.g. Introduction to Algebra">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="youtube_url">YouTube URL <span class="text-danger">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" id="youtube_url" class="form-control" name="youtube_url" required
                                placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxx">
                            <small class="form-text text-muted">
                                Accepted formats: <code>youtube.com/watch?v=ID</code>, <code>youtu.be/ID</code>, <code>youtube.com/embed/ID</code>, <code>youtube.com/shorts/ID</code>, or just the 11-char video ID.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="description">Description</label>
                        <div class="col-sm-7">
                            <textarea id="description" class="form-control" name="description" rows="3" placeholder="Optional"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="class_id">Class</label>
                        <div class="col-sm-7">
                            <select id="class_id" class="form-control" name="class_id">
                                <option value="">-- Optional --</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['class_id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-7">
                            <button type="submit" class="btn btn-primary"><i class="entypo-plus"></i> Add Media</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left"><?php echo get_phrase('media_list'); ?></h4>
            </div>
            <div class="panel-body">
                <?php if (empty($medias)): ?>
                    <p class="text-muted">No media added yet.</p>
                <?php else: ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th style="width:300px;">Preview</th>
                                <th>Class</th>
                                <th>Added</th>
                                <th style="width:100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($medias as $m): ?>
                            <tr>
                                <td><?php echo $m['media_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($m['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($m['description']); ?></td>
                                <td><?php echo $m['mlink']; ?></td>
                                <td>
                                    <?php
                                        if (!empty($m['class_id'])) {
                                            $cls = $this->db->get_where('class', array('class_id' => $m['class_id']))->row();
                                            echo $cls ? htmlspecialchars($cls->name) : '-';
                                        } else { echo '-'; }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($m['timestamp']); ?></td>
                                <td>
                                    <a href="<?php echo base_url(); ?>index.php?admin/media/delete/<?php echo $m['media_id']; ?>"
                                       class="btn btn-danger btn-xs"
                                       onclick="return confirm('Delete this media?');">
                                        <i class="entypo-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
