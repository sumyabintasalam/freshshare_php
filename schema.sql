
CREATE DATABASE IF NOT EXISTS freshshare;
USE freshshare;

CREATE TABLE IF NOT EXISTS Users (
  user_id        INT AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(100) NOT NULL,
  email          VARCHAR(150) NOT NULL UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  area           VARCHAR(100),
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Inventory_Items (
  item_id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT NOT NULL,
  item_name        VARCHAR(100) NOT NULL,
  category         VARCHAR(50),
  quantity         DECIMAL(6,2) NOT NULL,
  unit             VARCHAR(20) NOT NULL,
  expiration_date  DATE NOT NULL,
  CONSTRAINT fk_items_user
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
    ON DELETE CASCADE,
  INDEX idx_expiration_date (expiration_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Shared_Listings (
  listing_id    INT AUTO_INCREMENT PRIMARY KEY,
  item_id       INT NOT NULL,
  pickup_note   VARCHAR(255),
  listed_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_available  BOOLEAN NOT NULL DEFAULT TRUE,
  CONSTRAINT fk_listings_item
    FOREIGN KEY (item_id) REFERENCES Inventory_Items(item_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Claims (
  claim_id      INT AUTO_INCREMENT PRIMARY KEY,
  listing_id    INT NOT NULL,
  claimed_by    INT NOT NULL,
  claimed_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  claim_status  ENUM('Pending', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  CONSTRAINT fk_claims_listing
    FOREIGN KEY (listing_id) REFERENCES Shared_Listings(listing_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_claims_user
    FOREIGN KEY (claimed_by) REFERENCES Users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
