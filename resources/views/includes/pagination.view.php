<?php
// 1. Buscamos automáticamente cuál es el arreglo de la paginación activa
$source = null;
foreach (get_defined_vars() as $key => $value) {
    if (is_array($value) && isset($value['current_page']) && isset($value['total']) && isset($value['last_page'])) {
        $source = $value;
        break;
    }
}

// 2. Si se encontró la paginación, limpiamos el entorno y generamos las URLs de forma segura
if ($source):
    $paginationUrlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Capturamos los parámetros de búsqueda actuales (como ?search=) y limpiamos 'page' y 'url'
    $cleanParams = $_GET;
    unset($cleanParams['page'], $cleanParams['url']);
    $paginationQueryString = count($cleanParams) > 0 ? '&' . http_build_query($cleanParams) : '';

    $currentPage = (int)$source['current_page'];
    $lastPage    = (int)$source['last_page'];

    $prevLink = $currentPage > 1 ? $paginationUrlPath . "?page=" . ($currentPage - 1) . $paginationQueryString : '#';
    $nextLink = $currentPage < $lastPage ? $paginationUrlPath . "?page=" . ($currentPage + 1) . $paginationQueryString : '#';
?>
<div class="row" style="margin-top: 15px; margin-left: 0; margin-right: 0">
    <div class="col-sm-5">
        <div class="dataTables_info" style="padding-top: 8px; padding-left: 0">
            Mostrando de <strong><?= (int)$source['from'] ?></strong> a <strong><?= (int)$source['to'] ?></strong> de <strong><?= (int)$source['total'] ?></strong> registros
        </div>
    </div>
    <div class="col-sm-7 text-right" style="padding-right: 0;">
        <ul class="pagination pagination-sm no-margin">
            <!-- Botón Anterior -->
            <li class="<?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a href="<?= $prevLink ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Números de Página -->
            <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                <li class="<?= $currentPage === $i ? 'active' : '' ?>">
                    <a href="<?= $paginationUrlPath ?>?page=<?= $i ?><?= $paginationQueryString ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Botón Siguiente -->
            <li class="<?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
                <a href="<?= $nextLink ?>" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php endif; ?>
