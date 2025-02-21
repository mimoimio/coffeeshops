<?php

define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'root');
define('DATABASE_PASSWORD', '');
define('DATABASE_NAME', 'coffeedb');

// Database connection
$db = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
if (mysqli_connect_errno()) {
    die("Connection failled " . mysqli_connect_error());
}

$coffeeshops = mysqli_fetch_all(mysqli_query($db, "SELECT * FROM shops"), MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_coffee_shop'])) {
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $hours_open = $_POST['hours_open'] ?? '';
    $image_dir = 'images/';
    // $

    if (empty($name) || empty($location) || empty($rating) || empty($hours_open)) {
        $create_coffee_shop_error = 'All fields are required';
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_dir .= $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_dir);
    }

    $query = "INSERT INTO shops (name, location, rating, hours_open, image_dir) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, 'ssiss', $name, $location, $rating, $hours_open, $image_dir);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: /admin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Coffee Shop Finder</title>
</head>

<body class="antialiased bg-gray-100">
    <?php if ($_SERVER['REQUEST_URI'] == '/admin'): ?>
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold mb-4">Admin Panel</h2>
                <p class="mb-4">Welcome to the Admin Panel</p>

                <a href="/admin/new-coffee-shop" class="bg-green-500 hover:bg-green-300 text-white p-4 rounded-lg">
                    Add New Coffee Shop
                </a>
                <table>
                    <thead>
                        <tr>
                            <th class="py-3 px-6 text-center">Name</th>
                            <th class="py-3 px-6 text-center">Location</th>
                            <th class="py-3 px-6 text-center">Rating</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-500 text-sm font-light">
                        <?php foreach ($coffeeshops as $shop): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-400">
                                <td class="py-3 px-6 text-center">
                                    <?php echo htmlspecialchars($shop['name']); ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <?php echo htmlspecialchars($shop['location']); ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <?php echo htmlspecialchars($shop['rating']); ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <a href="/admin/edit-coffee-shop?id=<?php echo $shop['id'] ?>">
                                        Edit
                                    </a>
                                    <form action="/admin/delete-coffee-shop?id=<?php echo $shop['id'] ?>">
                                        <button type="submit" name="delete_coffee_shop">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php elseif ($_SERVER['REQUEST_URI'] == '/admin/new-coffee-shop'): ?>
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold mb-4">Add New Coffee Shop</h2>
                <?php if (isset($create_coffee_shop_error)): ?>
                    <p class="text-red-500 mb-4"><?php echo $create_coffee_shop_error ?></p>
                <?php endif ?>

                <form method="post" action="/admin/new-coffee-shop" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="name" class="block mb-2">Name</label>
                        <input type="text" id="name" name="name" required class="w-full p-2 mb-4 bg-white">
                    </div>
                    <div class="mb-4">
                        <label for="location" class="block mb-2">Location</label>
                        <input type="text" id="location" name="location" required class="w-full p-2 mb-4 bg-white">
                    </div>
                    <div class="mb-4">
                        <label for="rating" class="block mb-2">Rating</label>
                        <input type="number" id="rating" name="rating" required class="w-full p-2 mb-4 bg-white">
                    </div>
                    <div class="mb-4">
                        <label for="hours_open" class="block mb-2">Hours Open</label>
                        <input type="text" id="hours_open" name="hours_open" required class="w-full p-2 mb-4 bg-white">
                    </div>
                    <div class="mb-4">
                        <label for="image" class="block mb-2">Image</label>
                        <input type="file" id="image" name="image" class="w-full p-2 mb-4 bg-white">
                    </div>
                    <button type="submit" name="create_coffee_shop" class="bg-green-500 hover:bg-green-300 text-white p-4 rounded-lg">
                        Add Coffee Shop
                    </button>
                </form>
            </div>
        </section>
    <?php else: ?>
        <main class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold mb-4">Coffee Shops</h2>
                <div class="flex flex-col gap-4">
                    <?php foreach ($coffeeshops as $shop): ?>
                        <div class="bg-white flex w-full rounded-lg shadow-md overflow-hidden">
                            <img
                                class="object-cover mx-4 my-4 bg-gray-100 rounded-lg p-4"
                                src="/<?php echo $shop['image_dir']; ?>"
                                alt="<?php echo $shop['name'] ?>"
                                width="300" height="300">
                            <div class="p-6 flex flex-col w-full justify-between">
                                <h2 class="text-xl font-semibold mb-2"><?php echo $shop['name']; ?></h2>
                                <p class="text-gray-600 mb-4"><?php echo $shop['location']; ?></p>
                                <div class="flex justify-between items-center w-full">
                                    <span class="text-yellow-500"><?php echo $shop['name']; ?></span>
                                    <a href="/shop/<?php echo urlencode($shop['name']); ?>" class="bg-green-500 py-3 px-6 rounded-lg text-yellow-200">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    <?php endif ?>

</body>

</html>