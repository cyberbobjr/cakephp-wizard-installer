# cakephp-wizard-installer
Un plugin CakePHP permettant de faire un wizard d'installation / mise à jour d'une application automatisée

Ce plugin est destiné avant tout pour mon usage interne et mes nombreux projets CakePHP, mais chacun est libre de l'utiliser comme il le veut.

Il y'a 2 routes principales :

``/install``

Pour une installation initiale, le script va chercher les fichiers de migration dans /config/Migrations de l'application.

``/update``

Pour une mise à jour de l'application, là aussi le système va chercher ses fichiers de migration dans /config/Migrations

# Lors de l'installation initiale
- Après saisie des informations par l'administrateur, le système va créer les fichiers de configurations de la bdd dans un fichier /config/app_gso.php
Il est donc nécessaire de modifier le fichier bootstrap.php pour charger ce fichier dans le framework CakePHP, je fais cela de cette façon :

``
if (file_exists(CONFIG . 'app_gso.php')) {
        Configure::load('app_gso', 'default');
    }
 ``
 
 - Au cours de l'installation, le système créé les comptes utilisateurs via un modèle de classe déclaré dans un autre plugin de mon cru "UserManager". Cette création de compte devra être personnalisée selon votre application.
 C'est cette fontion dans "InstallController.php" :
 
 ```
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
    ```
   
   Une fois l'installation terminé, le système va créer un fichier install.lock à la racine de votre application. Ce fichier est vérifié pour autoriser ou non l'installation (si install.lock présent => interdit, si install.lock absent => autorisé)
   
# Pour les mises à jour
Le fichier qui est vérifié est update.lock, il faut donc ajouter / supprimer manuellement ce fichier à la racine de l'application pour autoriser ou non les mises à jour.
Les mises à jour sont manuelles, et se basent sur les fichiers de migrations situées dans /config/migrations

# Bootstrapping route
Par défaut, si le fichier install.lock n'est pas trouvé, le plugin bootstrap *toutes* les routes de l'application et force la redirection vers le plugin d'installation. Pour modifier ce comportement modifier le fichier WizardInstaller/config/bootstrap.php

# Conclusion
C'est un plugin utile pour moi, qui n'est pas forcément publiable en l'état et qui devra être modifié si vous en avez besoin.
Je reste à votre disposition si vous avez des questions, ou si vous avez des suggestions d'améliorations je suis preneur (PR inside ou Issue requests ;))
