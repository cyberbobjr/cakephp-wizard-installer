<?php
$bdd = json_decode($this->request->session()
                                 ->read('bdd'));
$admin = json_decode($this->request->session()
                                   ->read('admin'));
?>
<?php $this->start('title') ?>
Récapitulatif des informations de configuration
<?php $this->end(); ?>

<?php $this->start('panel-content'); ?>
<table class="table table-striped">
    <thead>
    <tr>
        <th colspan="2">Information sur la clef de cryptage</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Clef de cryptage</td>
        <td>
            <?= $bdd->clef ?>
        </td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="2">Informations sur la base de données</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Hôte du serveur MySQL</td>
        <td><?= $bdd->host ?></td>
    </tr>
    <tr>
        <td>Nom de la base de données</td>
        <td><?= $bdd->database ?></td>
    </tr>
    <tr>
        <td>Login de connexion</td>
        <td><?= $bdd->username ?></td>
    </tr>
    <thead>
    <tr>
        <th colspan="2">Information de connexion au compte admin</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Login de connexion administrateur</td>
        <td><?= $admin->login ?></td>
    </tr>
    <tr>
        <td>Nom / prénom de l'administrateur</td>
        <td><?= $admin->nom . ' ' . $admin->prenom ?></td>
    </tr>
    <tr>
        <td>Courriel de l'administrateur</td>
        <td><?= $admin->courriel ?></td>
    </tr>
    </tbody>
</table>
<div class="alert alert-danger">
    En cliquant sur le bouton "Valider et enregistrer la configuration" vous ne pourrez plus revenir en arrière et cet
    écran d'installation ne sera plus disponible.
    Assurez-vous que toutes les informations saisies soient correctes.
    <br/>
    Si vous désirez pouvoir refaire une installation, vous devrez supprimer le fichier install.lock qui se trouvera à la
    racine de votre installation.
</div>
<div class="text-center">
    <?= $this->Form->postLink(__('Valider et enregistrer la configuration'), ['action' => 'step',
                                                                              4], ['class' => 'btn btn-lg btn-warning']) ?>
</div>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
<div class="pull-left">
    <?= $this->Html->link('<i class="fa fa-chevron-left"></i>&nbsp;' . __('Etape précédente'), ['action' => 'step',
                                                                                                2], [
        'class'  => 'btn btn-sm btn-success',
        'escape' => FALSE]) ?>
</div>
<?php $this->end(); ?>

