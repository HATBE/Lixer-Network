        <?php if(!isset($noparts)):?>
        </main>
        <footer>
            <div class="p-3 container d-flex text-center justify-content-center flex-column text-light">
                <div>
                    &copy; 2022 - <a class="link-light text-decoration-none" href="https://hatbe.ch">HATBE</a>
                </div>
                <div>
                    <small>
                        <a class="link-light" href="<?= ROOT_PATH;?>imprint">Imprint</a>
                    </small> 
                </div>
            </div>
        </footer>
        <?php endif;?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="<?= ROOT_PATH;?>assets/js/script.js"></script>
    </body>
</html>