<?php
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document'])) {
    $docNum = intval($_POST['document']);
    if ($docNum >= 1 && $docNum <= TOTAL_DOCS) {
        writeLog('STAGE1', "✅ Document {$docNum} accepted.");
    }
    exit();
}
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['legalpage'] ?? ''); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
      <p class="lead"><?php echo e($translations['youcanacceptsomethings'] ?? ''); ?></p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <div class="card">
        <div class="card-body">
          <embed id="documentViewer"
                 src="../assets/docs/document1.pdf#toolbar=0&navpanes=0&scrollbar=0"
                 type="application/pdf" width="100%" height="400px">
          <div class="form-check mt-3 d-flex align-items-center justify-content-center">
            <input type="checkbox" class="form-check-input custom-checkbox"
                   id="acceptTerms" onchange="toggleButton()">
            <label class="form-check-label ms-2" for="acceptTerms">
              <?php echo e($translations['accept-docs'] ?? ''); ?>
            </label>
          </div>
          <button id="continueButton" class="btn btn-primary mt-3" disabled>
            <?php echo e($translations['continue'] ?? 'Continue'); ?>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  let currentDoc  = 1;
  const totalDocs = <?php echo TOTAL_DOCS; ?>;

  function toggleButton() {
    document.getElementById('continueButton').disabled =
      !document.getElementById('acceptTerms').checked;
  }

  document.getElementById('continueButton').addEventListener('click', function () {
    fetch('', {
      method : 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body   : 'document=' + currentDoc,
    });

    if (currentDoc < totalDocs) {
      currentDoc++;
      document.getElementById('documentViewer').src =
        `../assets/docs/document${currentDoc}.pdf#toolbar=0&navpanes=0&scrollbar=0`;
      document.getElementById('acceptTerms').checked = false;
      this.disabled = true;
    } else {
      window.location.href = '../stage2/';
    }
  });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>