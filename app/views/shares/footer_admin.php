            </div> <!-- End main content -->
            </div>
            </div>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                // Tự động ẩn alert sau 3 giây
                document.addEventListener('DOMContentLoaded', function() {
                    // Tìm tất cả các alert
                    var alerts = document.querySelectorAll('.alert');

                    // Với mỗi alert, set timeout để ẩn
                    alerts.forEach(function(alert) {
                        setTimeout(function() {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }, 3000);
                    });
                });
            </script>
            </body>

            </html>