<?php
require_once 'config.php';
// logout.php

session_unset();
session_destroy();
header('Location: ../../login');
exit;