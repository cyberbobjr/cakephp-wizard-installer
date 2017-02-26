<?php

$valid = TRUE;
?>
<?= $this->Form->create(NULL) ?>
<?php $this->start('panel-content'); ?>
<fieldset>
    <legend>Paramétrage du compte administrateur</legend>
    <?= $this->Form->input('login', ['label' => __('Login de l\'administrateur'),
                                     'required']); ?>
    <?= $this->Form->input('password', ['label' => __('Mot de passe de l\'administrateur'),
                                        'required']); ?>
    <?= $this->Form->input('confirmpassword', ['label' => __('Confirmer le mot de passe de l\'administrateur'),
                                               'type'  => 'password',
                                               'required']); ?>
    <legend>Informations de l'utilisateur</legend>
    <?= $this->Form->input('nom', ['label' => __('Nom de l\'administrateur'),
                                   'required']); ?>
    <?= $this->Form->input('prenom', ['label' => __('Prénom de l\'administrateur'),
                                      'required']); ?>
    <?= $this->Form->input('courriel', ['label' => __('Courriel de l\'administrateur'),
                                        'required']); ?>
</fieldset>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
<div class="pull-left">
    <?= $this->Html->link('<i class="fa fa-chevron-left"></i>&nbsp;' . __('Etape précédente'), ['action' => 'step',
                                                                                                2], [
        'class'  => 'btn btn-sm btn-success',
        'escape' => FALSE]) ?>
</div>
<div class="pull-right">
    <?= $this->Form->button(__('Etape suivante') . '&nbsp;<i class="fa fa-chevron-right"></i>', [
        'name'     => 'step4',
        'class'    => 'btn btn-sm btn-success',
        'disabled' => !$valid]) ?>
</div>
<?= $this->Form->end() ?>
<?php $this->end(); ?>
