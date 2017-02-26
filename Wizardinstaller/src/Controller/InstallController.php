<?php
namespace Wizardinstaller\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Migrations\Migrations;

/**
 * Wizard Controller
 *
 */
class InstallController extends AppController
{

// étape vérification pré-requis : urlrewriting et chmod
// étape 1 : URL du site
// étape 2 : Configuration BDD (host, login, mot de passe) et test
// étape 3 : Création d'un compte admin+mot de passe et création des tables users/groupes/droits
// étape 3 : Validation et enregistrement

    /**
     * Le fichier de configuration de la BDD doit être dans un fichier indépendant, ce qui permet de le générer lorsque
     * la BDD est validée
     * Une fois la configuration enregistrée, un fichier install.lock est créé.
     * C'est ce fichier install.lock qui est vérifié pour déterminer si l'application doit être installée ou est déjà
     * paramétrée
     * En supprimant le fichier install.lock l'utilisateur peut refaire une installation propre de GSO
     */

    /**
     * Page principale d'installation
     * @param int $step Numéro de l'étape en cours
     * @return \Cake\Network\Response|null
     */
    public function step($step = 1)
    {
        if (file_exists(ROOT . DS . 'install.lock')) {
            throw new ForbiddenException(__('L\'installation a déjà été paramétrée, supprimez le fichier install.lock pour recommencer l\'installation.'));
        }
        // vérification anti petits malins
        if ($step > 5) {
            $this->Flash->error(__('Etape inconnue'));
            return $this->redirect(['action' => 'step',
                                    1]);
        }

        /**
         * Si c'est un simple affichage de la page
         */
        if ($this->request->is('get')) {
            // en fonction de l'étape
            switch ($step) {
                case 1 :
                    break;
                case 2 :
                    // gestion des informations de la bdd
                    $this->_readSession('bdd');
                    break;
                case 3 :
                    // gestion du compte administrateur
                    $this->_readSession('admin');
                    break;
                case 4:
                    // vérification que les données en session soient bien présentes
                    if (!$this->_ready()) {
                        $this->Flash->error(__('Elements manquants, redirection vers la page principale de configuration'));
                        return $this->redirect(['action' => 'step',
                                                1]);
                    }
                    // récapitulatif des informations et sauvegarde définitive
                    break;
                case 5:
                    $valid = TRUE;
                    $bdd = json_decode($this->request->session()
                                                     ->read('bdd'), TRUE);
                    $notcreate = $bdd['notcreate'];
                    if (!$notcreate) {
                        // création des tables et enregistrement de l'utilisateur
                        if ($this->_generateTables()) {
                            $this->Flash->success(__('Tables créées'));
                            // les tables ont été créées
                            // création des droits
                            if ($this->_creerDroits()) {
                                $this->Flash->success(__('Droits créés'));
                            } else {
                                $this->Flash->error(__('Erreur durant la création des droits'));
                                $valid = FALSE;
                            }
                        } else {
                            $this->Flash->error(__('Erreur durant la création des tables'));
                            $valid = FALSE;
                        }
                    }
                    /**
                     * Création du compte administrateur
                     */
                    if ($this->_creerCompte()) {
                        $this->Flash->success(__('compte administrateur créé'));
                    } else {
                        $this->Flash->error(__('Erreur durant la création du compte administrateur'));
                        $valid = FALSE;
                    }
                    if ($valid) {
                        // création du fichier install.lock
                        touch(ROOT . DS . 'install.lock');
                    }
                    $this->set('valid', $valid);
                    break;
            }
        }
        /**
         * Si c'est une requête de passage à l'étape suivante
         */
        if ($this->request->is('post')) {
            switch ($step) {
                case 1 :
                    // step1 : Informations et prérequis
                    break;
                case 2 :
                    // step2 : Renseignement url et BDD
                    // enregistrement de la configuration en session et passage au step suivant
                    $this->_saveSession('bdd', $this->request->data);
                    break;
                case 3:
                    // step3 : Renseignements admin/pwd
                    $this->_saveSession('admin', $this->request->data);
                    // vérification de la conformité du mot de passe
                    if (!$this->_checkCompteAdmin()) {
                        $this->Flash->error(__('Les mots de passe ne correspondent pas'));
                        return $this->redirect(['action' => 'step',
                                                $step]);
                    }
                    break;
                case 4:
                    // step4 : finalisation de l'installation
                    // vérification que les données en session soient bien présentes
                    if (!$this->_ready()) {
                        $this->Flash->error(__('Elements manquants, redirection vers la page principale de configuration'));
                        return $this->redirect(['action' => 'step',
                                                1]);
                    }
                    // enregistrement de la configuration
                    if ($this->_saveConfig()) {
                        $this->Flash->success(__('Configuration enregistrée'));
                        // la configuration s'est bien enregistrée, nous allons pouvoir créer les tables de l'application
                        return $this->redirect(['action' => 'step',
                                                5]);
                    } else {
                        $this->Flash->error(__('Erreur lors de l\'enregistrement de la configuration'));
                    }
                    return $this->redirect(['action' => 'step',
                                            4]);
                    // traitement de la sauvegarde, génération des fichiers de configurations
                    break;
            }
            // redirection vers l'étape suivante
            $step = $this->_getStep();
            return $this->redirect(['action' => 'step',
                                    $step]);
        }
        $this->set('step', $step);
    }

    /**
     * Récupération les informations en session
     * @param string $index Nom de la clef à récupérer en session
     */
    private function _readSession($index)
    {
        if (!is_null($this->request->session()
                                   ->read($index))
        ) {
            // récupération des informations de session si nécessaire pour la configuration de la bdd
            $this->request->data = json_decode($this->request->session()
                                                             ->read($index), TRUE);
        }
    }

    private function _ready()
    {
        return ($this->request->session()
                              ->check('admin') && $this->request->session()
                                                                ->check('bdd'));
    }

    /**
     * Génére les tables nécessaires pour l'application
     * @return bool TRUE en cas de succès, FALSE si échec
     */
    private function _generateTables()
    {
        $migrations = new Migrations();
        try {
            $success = $migrations->migrate();
        } catch (\Exception $ex) {
            $success = FALSE;
        }
        return $success;
    }

    /**
     * Création des droits dans la table droits
     * @return array|bool|\Cake\ORM\ResultSet
     */
    private function _creerDroits()
    {
        $success = TRUE;
        $migrations = new Migrations();
        try {
            $success = $migrations->seed(['seed' => 'DroitsSeed']);
        } catch (\Exception $ex) {
            $success = FALSE;
        }
        return $success;
    }

    /**
     * Création du compte admin
     * @return bool
     */
    private function _creerCompte()
    {
        $admin = json_decode($this->request->session()
                                           ->read('admin'), TRUE);
        $groupestable = TableRegistry::get('UserManager.groupes');
        $userstable = TableRegistry::get('UserManager.users');
        $groupe = $groupestable->findOrCreate(['label' => 'ADMIN'], function ($entity) {
            $entity->description = __('Super-administrateurs de l\'application');
        });
        $user = $userstable->newEntity(['username'   => $admin['login'],
                                        'password'   => $admin['password'],
                                        'courriel'   => $admin['courriel'],
                                        'nom'        => $admin['nom'],
                                        'prenom'     => $admin['prenom'],
                                        'last_login' => date('now')], ['validate' => FALSE]);
        $user = $userstable->save($user);
        if ($user && $groupe) {
            $userstable->Groupes->link($user, [$groupe]);
            $groupestable->Droits->link($groupe, [$user]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Sauvegarde les informations en session
     * @param string $index Nom de la clef à utiliser pour la session
     * @param array $data Tableau à sauvegarder
     */
    private function _saveSession($index, $data)
    {
        $this->request->session()
                      ->write($index, json_encode($data));
    }

    /**
     * Vérifie que le mot de passe est identique à la confirmation du mot de passe
     * @return bool
     */
    private function _checkCompteAdmin()
    {
        return ($this->request->data('password') == $this->request->data('confirmpassword'));
    }

    /**
     * Enregistre la configuration
     */
    private function _saveConfig()
    {
        // lancement des opérations de création de la configuration
        if ($this->_generateBdd() && $this->_generateClef()) {
            // la configuration a bien été générée, on peut enregistrer le fichier de configuration
            return Configure::dump('app_gso', 'default', ['Security',
                                                          'Datasources']);
        } else {
            return FALSE;
        }
    }

    /**
     * Génére le fichier de configuration Bdd
     * @return bool
     */
    private function _generateBdd()
    {
        $bdd = json_decode($this->request->session()
                                         ->read('bdd'), TRUE);
        $bdd['className'] = 'Cake\Database\Connection';
        $bdd['driver'] = 'Cake\Database\Driver\Mysql';
        $bdd['persistent'] = FALSE;
        $bdd['encoding'] = 'utf8';
        $bdd['timezone'] = 'UTC';
        $bdd['cacheMetadata'] = TRUE;
        unset($bdd['clef']);
        unset($bdd['step3']);
        unset($bdd['notcreate']);

        return Configure::write('Datasources.default', $bdd);
    }

    /**
     * Génére le fichier de configuration pour la clef de sécurité
     * @return bool
     */
    private function _generateClef()
    {
        $bdd = json_decode($this->request->session()
                                         ->read('bdd'), TRUE);
        return Configure::write('Security.salt', "env('SECURITY_SALT','" . $bdd['clef'] . "'')");
    }

    /**
     * Retourne l'étape demandée
     * @return int Numéro de l'étape
     */
    private function _getStep()
    {
        if (!is_null($this->request->data('step1'))) {
            return 1;
        }
        if (!is_null($this->request->data('step2'))) {
            return 2;
        }
        if (!is_null($this->request->data('step3'))) {
            return 3;
        }
        if (!is_null($this->request->data('step4'))) {
            return 4;
        }
        return 1;
    }

    /**
     * Fonction qui vérifie si les paramètres de la BDD sont corrects
     * Les paramètres sont en $_POST
     * Cette fonction est appelée en AJAX et renvoie un résultat JSON ok/nok
     */
    public function checkBdd()
    {
        // récupération des informations $_POST
        $host = $this->request->data('host');
        $login = $this->request->data('username');
        $pwd = $this->request->data('password');
        $bdd = $this->request->data('database');
        // si l'un des paramètres est vide, erreur
        if (is_null($host) || is_null($login) || is_null($pwd) || is_null($bdd)) {
            $result = ['success' => FALSE,
                       'msg'     => __('Les informations ne sont pas complètes')];
        } else {
            // paramètres non vide, nous testons la configuration
            if ($this->_checkBdd($host, $login, $pwd, $bdd)) {
                $result = ['success' => TRUE,
                           'msg'     => __('Connexion réussie')];
            } else {
                $result = ['success' => FALSE,
                           'msg'     => __('Les informations saisies ne sont pas correctes, erreur de connexion')];
            }
        }
        $this->set(compact('result'));
    }

    /**
     * Fonction de vérification des informations de connexion à la BDD
     * @param string $host Adresse du serveur MySQL
     * @param string $login Login du serveur MySQL
     * @param string $pwd Mot du passe du serveur
     * @param string $bdd Nom de la base de donnée
     * @return bool TRUE si connexion réussie, FALSE sinon
     */
    private function _checkBdd($host, $login, $pwd, $bdd)
    {

        ConnectionManager::config('testgso', ['className'     => 'Cake\Database\Connection',
                                              'driver'        => 'Cake\Database\Driver\Mysql',
                                              'persistent'    => TRUE,
                                              'host'          => $host,
                                              'username'      => $login,
                                              'password'      => $pwd,
                                              'database'      => $bdd,
                                              'encoding'      => 'utf8',
                                              'timezone'      => 'UTC',
                                              'cacheMetadata' => TRUE,
                                              'log'           => TRUE,]);
        try {
            $connection = ConnectionManager::get('testgso');
            $connected = $connection->connect();
        } catch (Exception $connectionError) {
            $connected = FALSE;
            $errorMsg = $connectionError->getMessage();
            if (method_exists($connectionError, 'getAttributes')):
                $attributes = $connectionError->getAttributes();
                if (isset($errorMsg['message'])):
                    $errorMsg .= '<br />' . $attributes['message'];
                    debug($errorMsg);
                endif;
            endif;
        }
        return $connected;
    }

    public function test()
    {
        //$this->_checkBdd('localhost', 'metavide_gse', 'm7CKsRitCtY8', 'metavide_gse');
        die();
    }
}
