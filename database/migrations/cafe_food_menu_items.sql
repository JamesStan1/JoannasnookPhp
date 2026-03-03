-- Cafe Food Menu Items Migration
-- Inserts all items from the /public/Cafe Food/ image folder.
-- Each statement is guarded with WHERE NOT EXISTS so re-running is safe.

-- ── Appetizers ───────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Bruschetta','Appetizer','Toasted bread topped with tomatoes and herbs',120.00,'/Cafe Food/Bruschetta.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Bruschetta');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Deviled Eggs','Appetizer','Classic creamy deviled eggs',150.00,'/Cafe Food/Deviled Eggs.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Deviled Eggs');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Dumplings','Appetizer','Steamed pork and vegetable dumplings',180.00,'/Cafe Food/Dumplings.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Dumplings');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Mozzarella Sticks','Appetizer','Golden fried mozzarella with marinara sauce',160.00,'/Cafe Food/Mozzarella Sticks.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Mozzarella Sticks');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Nachos','Appetizer','Crispy tortilla chips with cheese and salsa',200.00,'/Cafe Food/Nachos.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Nachos');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Onion Rings','Appetizer','Beer-battered crispy onion rings',120.00,'/Cafe Food/Onion Rings.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Onion Rings');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Shrimp Cocktail','Appetizer','Chilled shrimp with cocktail sauce',250.00,'/Cafe Food/Shrimp Cocktail.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Shrimp Cocktail');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Siomai','Appetizer','Steamed pork and shrimp dumplings',120.00,'/Cafe Food/Siomai.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Siomai');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Sisig Bites','Appetizer','Crispy sisig bites served with calamansi',200.00,'/Cafe Food/Sisig Bites.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Sisig Bites');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Spring Rolls','Appetizer','Crispy fried vegetable spring rolls',150.00,'/Cafe Food/Spring Rolls.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Spring Rolls');

-- ── Beverages ────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Buko Juice','Beverage','Fresh young coconut juice',80.00,'/Cafe Food/Buko Juice.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Buko Juice');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Calamansi Juice','Beverage','Refreshing calamansi juice',70.00,'/Cafe Food/Calamansi Juice.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Calamansi Juice');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Gulaman Drink','Beverage','Sweet gulaman in flavored syrup',60.00,'/Cafe Food/Gulaman Drink.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Gulaman Drink');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Mango Shake','Beverage','Thick and creamy fresh mango shake',120.00,'/Cafe Food/Mango Shake.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Mango Shake');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Ube Shake','Beverage','Creamy ube flavored shake',130.00,'/Cafe Food/Ube Shake.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Ube Shake');

-- ── Desserts ─────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Apple Pie','Dessert','Classic flaky crust apple pie',150.00,'/Cafe Food/Apple Pie.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Apple Pie');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Banana Split','Dessert','Banana with three scoops of ice cream and toppings',200.00,'/Cafe Food/Banana Split.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Banana Split');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Brownies','Dessert','Rich fudgy chocolate brownies',120.00,'/Cafe Food/Brownies.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Brownies');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chocolate Cake','Dessert','Rich layered chocolate cake',180.00,'/Cafe Food/Chocolate Cake.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chocolate Cake');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Crepes','Dessert','Thin French crepes with sweet fillings',150.00,'/Cafe Food/Crepes.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Crepes');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Fruit Salad','Dessert','Mixed fresh fruits in cream',130.00,'/Cafe Food/Fruit Salad.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Fruit Salad');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Halo-Halo','Dessert','Filipino shaved ice dessert with mixed sweet ingredients',150.00,'/Cafe Food/Halo-Halo.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Halo-Halo');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Ice Cream','Dessert','Scoop of premium ice cream',100.00,'/Cafe Food/Ice Cream.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Ice Cream');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Leche Flan','Dessert','Classic Filipino caramel custard',130.00,'/Cafe Food/Leche Flan.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Leche Flan');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Macarons','Dessert','Assorted French macarons',120.00,'/Cafe Food/Macarons.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Macarons');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Mango Float','Dessert','Layered mango cream float',160.00,'/Cafe Food/Mango Float.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Mango Float');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Panna Cotta','Dessert','Italian cream dessert with berry coulis',170.00,'/Cafe Food/Panna Cotta.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Panna Cotta');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Tiramisu','Dessert','Classic Italian coffee dessert',200.00,'/Cafe Food/Tiramisu.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Tiramisu');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Ube Cake','Dessert','Purple yam layer cake',190.00,'/Cafe Food/Ube Cake.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Ube Cake');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'White Chocolate Cheesecake','Dessert','Creamy white chocolate New York cheesecake',220.00,'/Cafe Food/White Chocolate Cheesecake.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'White Chocolate Cheesecake');

-- ── Main Course ──────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Baked Salmon','Main Course','Herb-baked salmon fillet',380.00,'/Cafe Food/Baked Salmon.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Baked Salmon');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'BBQ Ribs','Main Course','Slow-cooked BBQ pork ribs',450.00,'/Cafe Food/BBQ Ribs.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'BBQ Ribs');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Beef Bulalo','Main Course','Slow-simmered beef shank soup',350.00,'/Cafe Food/Beef Bulalo.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Beef Bulalo');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Beef Caldereta','Main Course','Filipino braised beef in tomato sauce',320.00,'/Cafe Food/Beef Caldereta.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Beef Caldereta');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Beef Steak','Main Course','Pan-seared beef steak with onions and soy',420.00,'/Cafe Food/Beef Steak.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Beef Steak');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Beef Tapa','Main Course','Cured beef tapa served with garlic rice and egg',250.00,'/Cafe Food/Beef Tapa.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Beef Tapa');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Bicol Express','Main Course','Pork cooked in coconut milk and chili',280.00,'/Cafe Food/Bicol Express.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Bicol Express');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Buttered Shrimp','Main Course','Garlic butter shrimp',350.00,'/Cafe Food/Buttered Shrimp.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Buttered Shrimp');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Curry','Main Course','Spiced chicken curry with rice',280.00,'/Cafe Food/Chicken Curry.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Curry');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Inasal','Main Course','Grilled marinated chicken inasal',250.00,'/Cafe Food/Chicken Inasal.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Inasal');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Teriyaki','Main Course','Japanese-style chicken teriyaki',300.00,'/Cafe Food/Chicken Teriyaki.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Teriyaki');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Wings','Main Course','Crispy fried chicken wings',280.00,'/Cafe Food/Chicken Wings.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Wings');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Fried Chicken','Main Course','Crispy golden fried chicken',220.00,'/Cafe Food/Fried Chicken.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Fried Chicken');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Grilled Chicken','Main Course','Herb-marinated grilled chicken',260.00,'/Cafe Food/Grilled Chicken.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Grilled Chicken');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Pork Adobo','Main Course','Classic Filipino pork adobo',260.00,'/Cafe Food/Pork Adobo.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Pork Adobo');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Pork Chop','Main Course','Grilled seasoned pork chop',300.00,'/Cafe Food/Pork Chop.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Pork Chop');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Pork Sisig','Main Course','Sizzling pork sisig with egg',260.00,'/Cafe Food/Pork Sisig.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Pork Sisig');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Roast Beef','Main Course','Slow-roasted beef with gravy',380.00,'/Cafe Food/Roast Beef.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Roast Beef');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Sinigang','Main Course','Filipino sour tamarind soup with pork',300.00,'/Cafe Food/Sinigang.png',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Sinigang');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Sweet and Sour Fish','Main Course','Crispy fish in sweet and sour sauce',320.00,'/Cafe Food/Sweet and Sour Fish.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Sweet and Sour Fish');

-- ── Pasta ────────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Baked Macaroni','Pasta','Oven-baked macaroni with meat sauce and cheese',220.00,'/Cafe Food/Baked Macaroni.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Baked Macaroni');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Carbonara','Pasta','Classic creamy Italian carbonara',230.00,'/Cafe Food/Carbonara.jpeg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Carbonara');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Alfredo','Pasta','Creamy Alfredo pasta with grilled chicken',250.00,'/Cafe Food/Chicken Alfredo.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Alfredo');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Creamy Mushroom Pasta','Pasta','Pasta in rich creamy mushroom sauce',240.00,'/Cafe Food/Creamy Mushroom Pasta.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Creamy Mushroom Pasta');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Filipino Spaghetti','Pasta','Sweet-style Filipino spaghetti with hotdog',180.00,'/Cafe Food/Filipino Spaghetti.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Filipino Spaghetti');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Lasagna','Pasta','Layered beef and cheese lasagna',260.00,'/Cafe Food/Lasagna.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Lasagna');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Mac and Cheese','Pasta','Creamy baked macaroni and cheese',200.00,'/Cafe Food/Mac and Cheese.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Mac and Cheese');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Spaghetti Bolognese','Pasta','Classic Italian meat sauce spaghetti',240.00,'/Cafe Food/Spaghetti Bolognese.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Spaghetti Bolognese');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Tuna Pasta','Pasta','Light tuna and olive oil pasta',220.00,'/Cafe Food/Tuna Pasta.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Tuna Pasta');

-- ── Salad ────────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Caesar Salad','Salad','Fresh romaine with parmesan and croutons',180.00,'/Cafe Food/Caesar Salad.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Caesar Salad');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Salad','Salad','Grilled chicken over mixed greens',200.00,'/Cafe Food/Chicken Salad.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Salad');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Coleslaw','Salad','Creamy shredded cabbage coleslaw',120.00,'/Cafe Food/Coleslaw.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Coleslaw');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Garden Salad','Salad','Fresh mixed garden vegetables',150.00,'/Cafe Food/Garden Salad.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Garden Salad');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Macaroni Salad','Salad','Creamy Filipino-style macaroni salad',130.00,'/Cafe Food/Macaroni Salad.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Macaroni Salad');

-- ── Snacks ───────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Cheese Quesadilla','Snacks','Grilled flour tortilla with melted cheese',200.00,'/Cafe Food/Cheese Quesadilla.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Cheese Quesadilla');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Chicken Nuggets','Snacks','Crispy bite-sized chicken nuggets',180.00,'/Cafe Food/Chicken Nuggets.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Chicken Nuggets');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Clubhouse Sandwich','Snacks','Triple-decker chicken and bacon sandwich',220.00,'/Cafe Food/Clubhouse Sandwich.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Clubhouse Sandwich');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Garlic Bread','Snacks','Toasted bread with garlic butter',100.00,'/Cafe Food/Garlic Bread.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Garlic Bread');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Mini Sandwiches','Snacks','Assorted bite-sized mini sandwiches',150.00,'/Cafe Food/Mini Sandwiches.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Mini Sandwiches');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Pizza Slice','Snacks','Single slice of classic pizza',150.00,'/Cafe Food/Pizza Slice.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Pizza Slice');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Potato Wedges','Snacks','Seasoned baked potato wedges',130.00,'/Cafe Food/Potato Wedges.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Potato Wedges');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Sandwich','Snacks','Classic deli sandwich',180.00,'/Cafe Food/Sandwich.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Sandwich');

-- ── Soup ─────────────────────────────────────────────────────────────────────
INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Clam Chowder','Soup','Creamy New England clam chowder',200.00,'/Cafe Food/Clam Chowder.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Clam Chowder');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Corn Soup','Soup','Sweet and creamy cream of corn soup',160.00,'/Cafe Food/Corn Soup.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Corn Soup');

INSERT INTO menu_items (name, category, description, price, image_url, active, created_at)
SELECT 'Cream of Mushroom','Soup','Rich and velvety mushroom soup',180.00,'/Cafe Food/Cream of Mushroom.jpg',1,NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Cream of Mushroom');
