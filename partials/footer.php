<?php
// ============================================================
//  GYM One Installer – HTML lábléc partial
// ============================================================
$assetBase = $assetBase ?? '../';
?>
<div class="footer-waves">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 8" fill="#252525">
    <path opacity="0.7" d="M0 8 V8 C20 0, 40 0, 60 8 V8z"></path>
    <path d="M0 8 V5 Q25 10 55 5 T100 4 V8z"></path>
  </svg>
</div>
<div class="footer">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4 mb-1">
        <h2 class="mb-4">
          <img src="https://gymoneglobal.com/assets/img/text-color-logo.png" alt="GYM One Logo" height="105">
        </h2>
        <p><?php echo $translations['herotext'] ?? ''; ?></p>
      </div>
      <div class="col-md-3 offset-md-1"></div>
      <div class="col-md-2 offset-md-1">
        <h2 class="text-light mb-4"><?php echo e($translations['links'] ?? ''); ?></h2>
        <ul class="list-unstyled links">
          <li><a href="<?php echo e(GITHUB_URL); ?>" target="_blank" rel="noopener noreferrer">GitHub</a></li>
          <li><a href="<?php echo e(DISCORD_URL); ?>" target="_blank" rel="noopener noreferrer">Discord</a></li>
          <li><a href="<?php echo e(SUPPORT_URL); ?>"><?php echo e($translations['support-us'] ?? ''); ?></a></li>
        </ul>
      </div>
    </div>
    <div class="border-top border-secondary pt-3 mt-3">
      <p class="small text-center mb-0">
        Copyright &copy; <?php echo date('Y'); ?> GYM One &ndash; <?php echo e($translations['copyright'] ?? ''); ?>.
        &nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
             class="bi bi-heart-fill" viewBox="0 0 16 16">
          <path fill-rule="evenodd"
                d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
        </svg>
        &ndash; <a href="https://www.mayerbalint.hu/">Mayer Bálint</a>
      </p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
</body>
</html>