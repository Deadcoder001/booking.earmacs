<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    header("Location: agent_login.php");
    exit;
}
?>

<!-- Include this in your HTML head -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="agent_dashboard.php">Agent Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text text-white me-3">
            Welcome, Agent
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-light" href="./agent_logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
