<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;

if (Configure::check('Wizardinstaller.background')) {
    $background = Configure::read('Wizardinstaller.background');
}
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?=
    $this->Html->css(['Wizardinstaller.bootstrap.min',
                      'Wizardinstaller.font-awesome.min',
                      'Wizardinstaller.font-google',
                      'Wizardinstaller.base'])
    ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <?= $this->Html->script(['Wizardinstaller.jquery-1.11.3.min',
                             'Wizardinstaller.bootstrap.min',
                             'Wizardinstaller.jquery.backstretch.min']) ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<!--main content start-->
<?= $this->Flash->render() ?>
<?= $this->Flash->render('auth') ?>
<div class="container">
    <div class="row-fluid">
        <div class="col-lg-12">
            <br/>
            <?= $this->fetch('content') ?>
        </div>
    </div>
</div>
<?php if (isset($background)): ?>
    <script>
        //$.backstretch("<?= $this->Url->build('/img/' . $background)?>", {speed: 500});
    </script>
<?php endif; ?>
<!--main content end-->
</body>
</html>
