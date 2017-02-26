<?php

?>
<script>
    $().ready(function () {
        $("input").on("keyup", function (ev) {
            $("button[name='step3']").attr('disabled', true);
        });
        $("input").on("change", function (ev) {
            $("button[name='step3']").attr('disabled', true);
        });

        /**
         * Sur clic du bouton de test des paramètres de la BDD
         */
        $("#btnTestCo").on("click", function (ev) {
            // serialization du form et envoi par AJAX
            $.ajax({
                url: "<?= $this->Url->build(['controller' => 'Install',
                                             'plugin'     => 'Wizardinstaller',
                                             'action'     => 'checkBdd',
                                             '_ext'       => 'json'
                ])?>",
                type: "POST",
                data: $("form").serialize(),
                dataType: "json",
                beforeSend: function () {
                    $("#loadingspinner").css('display', 'block');
                },
                success: function (json) {
                    $("#msgdiv").toggleClass('alert-danger', !json.result.success).toggleClass('alert-success', json.result.success);
                    $("#msgdiv").html(json.result.msg);
                    $("button[name='step3']").attr('disabled', !json.result.success);
                },
                error: function (err) {
                    $("#msgdiv").toggleClass('alert-danger', true);
                    $("#msgdiv").html(err);
                    console.log(err);
                },
                complete: function () {
                    $("#loadingspinner").css('display', 'none');
                }
            });
            ev.preventDefault();
            return false;
        });
    })
</script>
<?= $this->Form->create(NULL) ?>
<?php $this->start('panel-content'); ?>
<fieldset>
    <legend>Informations du site</legend>
    <?= $this->Form->input('clef', ['label'       => __('Clef alphanumérique de cryptage (lettres et chiffres uniquement)'),
                                    'pattern'     => '[a-zA-Z0-9]+',
                                    'placeholder' => __('Cette clef sera utilisée pour chiffrer les mots de passe dans la base'),
                                    'required']) ?>
</fieldset>
<fieldset>
    <legend>Informations de la base de données</legend>
    <?= $this->Form->input('host', ['label'       => __('Adresse du serveur de la base de données'),
                                    'placeholder' => __('Localhost par exemple'),
                                    'required']) ?>
    <?= $this->Form->input('database', ['label'       => __('Nom de la base de données'),
                                        'placeholder' => __('Nom de la base de donnée existante sur le serveur MySQL'),
                                        'required']) ?>
    <?= $this->Form->input('username', ['label'       => __('Utilisateur de la base de données'),
                                        'placeholder' => __('Compte utilisateur ayant les droits sur la base de données'),
                                        'required']) ?>
    <?= $this->Form->input('password', ['label'       => __('Mot de passe de l\'utilisateur'),
                                        'placeholder' => __('Mot de passe'),
                                        'required']) ?>
    <?= $this->Form->checkbox('notcreate') ?>
    <?= $this->Form->label('notcreate', __('Cochez cette case si vous <u class="text-danger">ne voulez pas</u> que les tables soient créées (dans le cas où vous utiliseriez une base existante)'), ['hiddenField' => FALSE,
                                                                                                                                                                                                     'escape'      => FALSE]) ?>
</fieldset>
<fieldset>
    <div class="text-center">
        <div id="loadingspinner" style="display: none" class="text-center">
            <i class="fa fa-spinner fa-spin fa-fw"></i>
        </div>
        <div class="alert" id="msgdiv"></div>
        <?= $this->Html->link('<i class="fa fa-plug"></i>&nbsp;' . __('Tester la connexion'), '#', ['class'  => 'btn btn-md btn-primary',
                                                                                                    'id'     => 'btnTestCo',
                                                                                                    'escape' => FALSE]) ?>
    </div>
</fieldset>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
<div class="pull-left">
    <?= $this->Html->link('<i class="fa fa-chevron-left"></i>&nbsp;' . __('Etape précédente'), ['action' => 'step',
                                                                                                1], ['class'  => 'btn btn-sm btn-success',
                                                                                                     'escape' => FALSE]) ?>
</div>
<div class="pull-right">
    <?= $this->Form->button(__('Enregistrer et passer à l\'étape suivante') . '&nbsp;<i class="fa fa-chevron-right"></i>', ['name'        => 'step3',
                                                                                                                            'disabled',
                                                                                                                            'class'       => 'btn btn-sm btn-success',
                                                                                                                            'data-toggle' => 'tooltip',
                                                                                                                            'title'       => __('Pour enregistrer la configuration, vous devrez tester la connexion')]) ?>
</div>
<?= $this->Form->end() ?>
<?php $this->end(); ?>
