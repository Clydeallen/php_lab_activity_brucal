<?php
require_once "dbcon.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

$statement = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$statement->bind_param("i", $id);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$user) {
    header("Location: index.php");
    exit;
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please provide a name and a valid email address.";
    } else {
        $statement = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $statement->bind_param("ssi", $name, $email, $id);
        if ($statement->execute()) {
            header("Location: index.php?msg=updated");
            exit;
        }
        $message = "Unable to update the user. Please try again.";
        $statement->close();
    }
    $user["name"] = $name;
    $user["email"] = $email;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5">
        <div>
          <div class="p-4">
            <h1 class="h3 mb-4">Edit User</h1>
            <?php if ($message !== ""): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST" action="edit.php?id=<?php echo $id; ?>">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user["name"]); ?>" required>
              </div>
              <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
              </div>
              <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary flex-grow-1">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>