<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
</head>
<body>
    <h1>Add Property</h1>
    <form action="addpro.php" method="post" enctype="multipart/form-data">
        <label for="property_name">Property Name:</label>
        <input type="text" name="property_name" required><br><br>

        <label for="property_price">Property Price:</label>
        <input type="number" name="property_price" step="0.01" required><br><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone"><br><br>

        <label for="description">Description:</label>
        <textarea name="description"></textarea><br><br>

        <label for="location">Location:</label>
        <input type="text" name="location"><br><br>

        <label for="state">State:</label>
        <input type="text" name="state"><br><br>

        <label for="city">City:</label>
        <input type="text" name="city"><br><br>

        <label for="status">Status:</label>
        <input type="text" name="status"><br><br>

        <label for="type">Type:</label>
        <input type="text" name="type"><br><br>

        <label for="min_bed">Min Beds:</label>
        <input type="number" name="min_bed"><br><br>

        <label for="min_baths">Min Baths:</label>
        <input type="number" name="min_baths"><br><br>

        <label for="min_kitchen">Min Kitchens:</label>
        <input type="number" name="min_kitchen"><br><br>

        <label for="min_hall">Min Halls:</label>
        <input type="number" name="min_hall"><br><br>

        <label for="min_balcony">Min Balconies:</label>
        <input type="number" name="min_balcony"><br><br>

        <label for="bhk">BHK:</label>
        <input type="text" name="bhk"><br><br>

        <label for="property_geo">Property Geo:</label>
        <input type="number" name="property_geo"><br><br>

        <label for="other_details">Other Details:</label>
        <textarea name="other_details"></textarea><br><br>

        <label for="main_image">Main Image:</label>
        <input type="file" name="main_image" required><br><br>

        <label for="floorplanimage">Floor Plan Image:</label>
        <input type="file" name="floorplanimage" required><br><br>

        <label for="terms_accepted">Terms Accepted:</label>
        <input type="checkbox" name="terms_accepted" value="1"><br><br>

        <label for="photo_data">Photos:</label>
        <input type="file" name="photo_data[]" multiple><br><br>

        <label for="video_data">Videos:</label>
        <input type="file" name="video_data[]" multiple><br><br>

        <input type="submit" name="submit" value="Add Property">
    </form>
</body>
</html>