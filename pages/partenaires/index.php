<?php
/**
 * Liste des partenaires
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Partenaires';
require_once __DIR__ . '/../../includes/header.php';

$partenaireClass = new Partenaire();
$partners = $partenaireClass->getAll();
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Partenaires</h1>
        <a href="create.php" class="btn btn-success">Nouveau Partenaire</a>
    </div>
    
    <div class="card">
        <table id="partnersTable" class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Personne de contact</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Site web</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partners as $partner): ?>
                    <tr>
                        <td><?php echo escape($partner['name']); ?></td>
                        <td>
                            <?php 
                            $types = [
                                'government' => 'Gouvernement',
                                'ngo' => 'ONG',
                                'private' => 'Privé',
                                'international' => 'International'
                            ];
                            echo $types[$partner['type']] ?? $partner['type'];
                            ?>
                        </td>
                        <td><?php echo escape($partner['contact_person'] ?? '-'); ?></td>
                        <td><?php echo escape($partner['phone'] ?? '-'); ?></td>
                        <td><?php echo escape($partner['email'] ?? '-'); ?></td>
                        <td>
                            <?php if ($partner['website']): ?>
                                <a href="<?php echo escape($partner['website']); ?>" target="_blank" style="color: var(--color-blue);">
                                    Visiter
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="status-badge-wrapper">
                                <?php echo getStatusBadge($partner['status'], 'general'); ?>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="view.php?id=<?php echo $partner['id']; ?>" class="btn-icon btn-icon-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $partner['id']; ?>" class="btn-icon btn-icon-success" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $partner['id']; ?>" class="btn-icon btn-icon-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#partnersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        order: [[0, 'asc']],
        columnDefs: [
            { 
                targets: -1, 
                orderable: false,
                searchable: false,
                className: 'actions-cell'
            }
        ],
        responsive: true,
        scrollX: true
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

