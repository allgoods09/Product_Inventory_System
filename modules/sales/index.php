<?php
require '../../includes/auth.php';
require '../../config/database.php';
$result=$conn->query("SELECT s.*,p.name FROM sales s JOIN products p ON s.product_id=p.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Sales</h1>
<a href="create.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">New Sale</a>
<table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
<tr class="bg-gray-100"><th class="px-4 py-2">Product</th><th class="px-4 py-2">Qty</th><th class="px-4 py-2">Total</th><th class="px-4 py-2">Date</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr class="hover:bg-gray-50">
<td class="px-4 py-2"><?php echo $row['name'];?></td>
<td class="px-4 py-2"><?php echo $row['quantity'];?></td>
<td class="px-4 py-2"><?php echo $row['total_amount'];?></td>
<td class="px-4 py-2"><?php echo $row['sale_date'];?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../../dashboard.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Back</a>
</body>
</html>
