<?= $this->fetch('content'); ?>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <h3 class="panel-title"><?= $this->fetch('title') ?>
            <div class="pull-right"><?= $this->fetch('header'); ?></div>
        </h3>
    </div>
    <div class="panel-body">
        <?php echo $this->Flash->render(); ?>
        <?= $this->fetch('panel-content'); ?>
    </div>
    <?php if ($this->exists('footer')): ?>
        <div class="panel-footer clearfix">
            <?= $this->fetch('footer'); ?>
        </div>
    <?php endif; ?>
</div>