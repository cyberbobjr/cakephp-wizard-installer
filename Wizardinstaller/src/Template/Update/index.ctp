<table class="table table-hovered">
    <thead>
    <tr>
        <th>Nom</th>
        <th>Status</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($migrations as $migration) : ?>
        <tr>
            <td><?= $migration['name'] ?></td>
            <td><?= $migration['status'] ?></td>
            <td>
                <?php if ($migration['status'] == 'down') : ?>
                    <?= $this->Html->link(__('Mise à jour'), [
                        'action' => 'applyMigration',
                        $migration['id']], ['class' => 'btn btn-success']); ?>
                    <?= $this->Html->link(__('Marquer comme terminée'), [
                        'action' => 'markMigrated',
                        $migration['id']], ['class' => 'btn btn-danger']); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table class="table table-hovered">
    <thead>
    <tr>
        <th>Nom</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($seeds as $seed) : ?>
        <tr>
            <td><?= $seed ?></td>
            <td>
                <?= $this->Html->link(__('Mise à jour'), [
                    'action' => 'applySeed',
                    '?'      => ['seed' => $seed]], ['class' => 'btn btn-success']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
