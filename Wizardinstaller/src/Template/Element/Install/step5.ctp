<?php $this->start('title') ?>
Résultat de l'installation
<?php $this->end(); ?>

<?php $this->start('panel-content'); ?>
<?php if ($valid): ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">L'application est installée</h3>
        </div>
        <div class="panel-body text-center">
            Configuration terminée, vous pouvez vous connectez.<br/>
            <?= $this->Html->link(__('Cliquez ici pour se connecter'), '/', ['class' => 'btn btn-md btn-success']); ?>
        </div>
    </div>
<?php else: ?>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">L'application n'a pas été installée correctement</h3>
        </div>
        <div class="panel-body">
            Une erreur est survenue, contactez le support pour plus d'informations en indiquant les erreurs mentionnées
            plus haut.
        </div>
    </div>
<?php endif; ?>
<?php $this->end(); ?>
