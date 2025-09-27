</main> <!-- /.container -->

<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. تمامی حقوق محفوظ است.</span>
    </div>
</footer>

<script>
    const URL_ROOT = '<?php echo URL_ROOT; ?>';
</script>
<!-- JQuery (required for Bootstrap and Summernote) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo URL_ROOT; ?>/js/main.js"></script>

</body>
</html>