<footer class="footer-uab py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
      <div>Universidad Autónoma del Beni "José Ballivián" — Trinidad, Beni, Bolivia</div>
      <div class="d-flex align-items-center gap-3">
        <span><i class="bi bi-envelope me-1"></i>eventos@uab.edu.bo</span>
        <span><i class="bi bi-shield-check me-1"></i>Certificación Criptográfica</span>
      </div>
    </div>
  </footer>

  <script src="<?= htmlspecialchars($baseUrl ?? '') ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const menu = document.getElementById('navbarMain');
      const closeButton = menu?.querySelector('[data-bs-dismiss="offcanvas"]');

      if (menu && closeButton && window.bootstrap?.Offcanvas) {
        closeButton.addEventListener('click', function () {
          bootstrap.Offcanvas.getOrCreateInstance(menu).hide();
        });
      }
    });
  </script>
</body>
</html>
