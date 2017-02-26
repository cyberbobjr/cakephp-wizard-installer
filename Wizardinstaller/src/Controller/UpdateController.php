<?php
namespace Wizardinstaller\Controller;

use Cake\Network\Exception\ForbiddenException;
use gso\gso;
use Migrations\Migrations;
use UserManager\Utility\Droits;

/**
 * Update Controller
 * Controller de mise à jour de l'application quand une nouvelle version de l'application est sortie
 **/
class UpdateController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        if (file_exists(ROOT . DS . 'update.lock')) {
            throw new ForbiddenException(__('L\'installation a déjà été paramétrée, supprimez le fichier install.lock pour recommencer l\'installation.'));
        }
        // récupération des migrations
        $migrations_coll = new Migrations();
        $migrations = $migrations_coll->status();
        // récupération des seeds
        $seeds = glob(ROOT . DS . "config/Seeds/*.php");
        $this->set(compact('migrations', 'seeds'));
    }

    /**
     * Applique une migration
     * @param $migration_ref
     */
    public function applyMigration($migration_ref)
    {
        $migrations = new Migrations();
        if ($migrations->migrate(['target' => $migration_ref])) {
            $this->Flash->success(__('Mise à jour terminée'));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * Applique le seed spécifié en paramètre query
     */
    public function applySeed()
    {
        $seed = basename($this->request->query('seed'), '.php');

        $migrations = new Migrations();
        if ($migrations->seed(['seed' => $seed])) {
            $this->Flash->success(__('Données créées'));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * Marque une migration comme migrée
     * @param $migration_ref
     */
    public function markMigrated($migration_ref)
    {
        $migrations = new Migrations();
        if ($migrations->markMigrated($migration_ref)) {
            $this->Flash->success(__('Migration marquée comme migrée'));
        }
        $this->redirect(['action' => 'index']);
    }
}
