<?php
$cur = $presets[$current['ui_theme']] ?? $presets['sunshine'];
$custom = $current['ui_primary'] !== '';
?>
<hr>
<?php echo form_open(base_url() . 'index.php?admin/theme_settings/save', array('id' => 'theme_form')); ?>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('choose_a_colour_theme'); ?></div></div>
            <div class="panel-body">
                <div class="row">
                <?php foreach ($presets as $key => $p): ?>
                    <div class="col-sm-4 col-xs-6">
                        <label class="theme-card <?php if ($current['ui_theme'] === $key) echo 'selected'; ?>" data-primary="<?php echo $p['primary']; ?>" data-accent="<?php echo $p['accent']; ?>">
                            <div class="sw" style="background:linear-gradient(135deg, <?php echo $p['sidebar_from']; ?>, <?php echo $p['sidebar_to']; ?>);"></div>
                            <div class="nm">
                                <input type="radio" name="ui_theme" value="<?php echo $key; ?>" <?php if ($current['ui_theme'] === $key) echo 'checked'; ?>>
                                <?php echo html_escape($p['label']); ?>
                                <?php if ($key === 'classic'): ?><small class="text-muted">(<?php echo get_phrase('original_look'); ?>)</small><?php endif; ?>
                                <span style="float:right;">
                                    <i style="display:inline-block;width:14px;height:14px;border-radius:50%;background:<?php echo $p['primary']; ?>;"></i>
                                    <i style="display:inline-block;width:14px;height:14px;border-radius:50%;background:<?php echo $p['accent']; ?>;"></i>
                                </span>
                            </div>
                        </label>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('customise'); ?></div></div>
            <div class="panel-body">
                <label style="font-weight:700;"><input type="checkbox" name="use_custom" id="use_custom" value="1" <?php if ($custom) echo 'checked'; ?>> <?php echo get_phrase('use_my_own_colours'); ?></label>
                <div id="custom_box" style="margin:10px 0 16px;<?php if (!$custom) echo 'opacity:.5;'; ?>">
                    <div class="form-group">
                        <label><?php echo get_phrase('main_colour'); ?></label>
                        <input type="color" name="ui_primary" id="ui_primary" class="form-control" style="height:42px;padding:4px;" value="<?php echo html_escape($custom ? $current['ui_primary'] : $cur['primary']); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo get_phrase('second_colour'); ?></label>
                        <input type="color" name="ui_accent" id="ui_accent" class="form-control" style="height:42px;padding:4px;" value="<?php echo html_escape($current['ui_accent'] !== '' ? $current['ui_accent'] : $cur['accent']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label><?php echo get_phrase('font'); ?></label>
                    <select name="ui_font" class="form-control">
                        <option value="nunito" <?php if ($current['ui_font'] === 'nunito') echo 'selected'; ?>>Nunito (<?php echo get_phrase('rounded_and_friendly'); ?>)</option>
                        <option value="baloo" <?php if ($current['ui_font'] === 'baloo') echo 'selected'; ?>>Baloo 2 (<?php echo get_phrase('playful'); ?>)</option>
                        <option value="system" <?php if ($current['ui_font'] === 'system') echo 'selected'; ?>><?php echo get_phrase('standard'); ?></option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-block"><i class="entypo-check"></i> <?php echo get_phrase('save_theme'); ?></button>
                <p class="text-muted" style="margin-top:10px;"><?php echo get_phrase('theme_applies_to_everyone_hint'); ?></p>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script>
(function () {
    var cards = document.querySelectorAll('.theme-card'), useCustom = document.getElementById('use_custom');
    cards.forEach(function (c) {
        c.addEventListener('click', function () {
            cards.forEach(function (x) { x.classList.remove('selected'); });
            c.classList.add('selected');
            if (!useCustom.checked) {      // show the preset's colours in the pickers
                document.getElementById('ui_primary').value = c.getAttribute('data-primary');
                document.getElementById('ui_accent').value = c.getAttribute('data-accent');
            }
        });
    });
    useCustom.addEventListener('change', function () { document.getElementById('custom_box').style.opacity = useCustom.checked ? 1 : .5; });
    // Live preview of the chosen colours on this page
    function preview() {
        if (!useCustom.checked) return;
        var r = document.documentElement.style;
        r.setProperty('--c-primary', document.getElementById('ui_primary').value);
        r.setProperty('--c-accent', document.getElementById('ui_accent').value);
    }
    document.getElementById('ui_primary').addEventListener('input', preview);
    document.getElementById('ui_accent').addEventListener('input', preview);
})();
</script>
