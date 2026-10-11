<?php
require_once "dbcon.php";

$message = "";
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name === "" || $email === "") {
        $message = "Please complete both fields.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } else {
        $statement = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $statement->bind_param("ss", $name, $email);

        if ($statement->execute()) {
            header("Location: index.php?msg=added");
            exit;
        }

        $message = "Unable to add the user. Please try again.";
        $messageType = "danger";
        $statement->close();
    }
}

if (isset($_GET["msg"])) {
    $messages = [
        "added" => "User added successfully.",
        "updated" => "User updated successfully.",
        "deleted" => "User deleted successfully."
    ];
    $message = $messages[$_GET["msg"]] ?? "";
}

$users = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="User management system">
  <title>User Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="py-4 mb-5">
    <div class="container text-center">
      <h1 class="fw-bold mb-1">User Management System</h1>
    </div>
  </header>

  <main class="container pb-5">
    <div class="row g-4 align-items-start">
      <section class="col-lg-4">
        <div>
          <div class="card-body p-4">
            <h2 class="h4 mb-4">Add New User</h2>
            <?php if ($message !== ""): ?>
              <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> d-flex justify-content-between align-items-center" role="alert">
                <span><?php echo htmlspecialchars($message); ?></span>
                <button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.remove()"></button>
              </div>
            <?php endif; ?>
            <form method="POST" action="index.php">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" placeholder="Enter Full Name" class="form-control" id="name" name="name" maxlength="20" required>
              </div>
              <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" placeholder="Enter Email Address" class="form-control" id="email" name="email" maxlength="20" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Add User</button>
            </form>
          </div>
        </div>
      </section>

      <section class="col-lg-8">
        <div>
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 class="h4 mb-0">Registered Users</h2>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th><th>Name</th><th>Email</th><th>Joined</th><th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($users->num_rows > 0): ?>
                    <?php while ($user = $users->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo (int) $user["id"]; ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($user["name"]); ?></td>
                        <td><?php echo htmlspecialchars($user["email"]); ?></td>
                        <td><?php echo htmlspecialchars(date("M d, Y", strtotime($user["created_at"]))); ?></td>
                        <td class="text-end text-nowrap">
                          <a href="edit.php?id=<?php echo (int) $user["id"]; ?>" class="btn btn-sm btn-primary">Edit</a>
                          <a href="delete.php?id=<?php echo (int) $user["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No data found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>
</html>