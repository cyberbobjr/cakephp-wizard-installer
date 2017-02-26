<?php
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\NotFoundException;

$valid = TRUE;
?>
<?= $this->Form->create(NULL, ['url' => ['action' => 'step',
                                         1]]) ?>
<?php $this->start('panel-content'); ?>
<div class="well">
    <p>Bienvenue sur la page d'installation de GSO. Vous allez être guidé pour installer GSO sur votre serveur.
        Munissez-vous des informations de connexion à votre base de donnée. Pour l'instant seul le moteur MySQL est
        supporté
        dans GSO.</p>

    <p>En cas de difficultés, connectez-vous sur la page de notre forum http:/xxxxx ou contactez le support si vous
        disposez d'un contrat de support.</p>
</div>
<h4>Vérification de l'environnement</h4>
<?php if (version_compare(PHP_VERSION, '5.5.9', '>=')): ?>
    <p class="alert alert-success">Votre version de PHP est supérieure à 5.5.9 (version detectée <?= PHP_VERSION ?>
        ).</p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">Votre version de PHP est trop ancienne, vous devez avoir au minimum la version 5.5.9
        pour installer GSO
        (version detectée <?= PHP_VERSION ?>).</p>
<?php endif; ?>
<?php if (extension_loaded('mbstring')): ?>
    <p class="alert alert-success">
        Votre version de PHP contient bien l'extension mbstring.
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre version de PHP ne contient PAS l'extension mbstring.
    </p>;
<?php endif; ?>

<?php if (extension_loaded('openssl')): ?>
    <p class="alert alert-success">
        Votre version de PHP contient l'extension openssl.
    </p>
<?php elseif (extension_loaded('mcrypt')): ?>
    <p class="alert alert-success">
        Votre version de PHP contient l'extension mcrypt.
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre version de PHP ne contient PAS les extensions openssl et mcrypt.
    </p>
<?php endif; ?>

<?php if (extension_loaded('intl')): ?>
    <p class="alert alert-success">
        Votre version de PHP contient l'extension intl.
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre version de PHP ne contient PAS l'extension intl.
    </p>
<?php endif; ?>
<h4>Vérification du système de fichier</h4>
<?php if (is_writable(TMP)): ?>
    <p class="alert alert-success">
        Votre répertoire temporaire est en écriture.
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre répertoire temporaire est en lecture seule.
    </p>
<?php endif; ?>

<?php if (is_writable(LOGS)): ?>
    <p class="alert alert-success">
        Votre répertoire de logs est en écriture.
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre répertoire de logs est en lecture seule.
    </p>
<?php endif; ?>

<?php $settings = Cache::config('_cake_core_'); ?>
<?php if (!empty($settings)): ?>
    <p class="alert alert-success">
        Le système de cache qui a été configuré est le moteur <em><?= $settings['className'] ?></em>
    </p>
<?php else: ?>
    <?php $valid = FALSE ?>
    <p class="alert alert-danger">
        Votre système de cache n'EST PAS configuré. Merci de vérifier votre fichier de configuration config/app.php
    </p>
<?php endif; ?>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
<div class="pull-right">
    <?= $this->Form->button(__('Etape suivante') . '&nbsp;<i class="fa fa-chevron-right"></i>', [
        'name'     => 'step2',
        'class'    => 'btn btn-sm btn-success',
        'disabled' => !$valid]) ?>
</div>
<?= $this->Form->end() ?>
<?php $this->end(); ?>
