# MakeMyLeague Database Schema

## Core Tables

### users
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
email (VARCHAR, NULLABLE)
mobile (VARCHAR, UNIQUE)
pin (VARCHAR)
photo (VARCHAR, NULLABLE)
position_id (FK -> game_positions.id, NULLABLE)
local_body_id (FK -> local_bodies.id, NULLABLE)
district_id (FK -> districts.id, NULLABLE)
slug (VARCHAR, NULLABLE)
country_code (VARCHAR, NULLABLE)
default_team_id (FK -> teams.id, NULLABLE)
remember_token
created_at, updated_at
```

### games
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
description (VARCHAR, NULLABLE)
image (VARCHAR, NULLABLE)
active (BOOLEAN, DEFAULT true)
created_at, updated_at
```

### game_positions
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
game_id (FK -> games.id)
created_at, updated_at
UNIQUE(name, game_id)
```

## Location Tables

### states
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
created_at, updated_at
```

### districts
```sql
id (PK, AUTO_INCREMENT)
state_id (FK -> states.id)
name (VARCHAR)
created_at, updated_at
```

### local_bodies
```sql
id (PK, AUTO_INCREMENT)
district_id (FK -> districts.id)
name (VARCHAR)
created_at, updated_at
```

### grounds
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
address (TEXT, NULLABLE)
localbody_id (FK -> local_bodies.id)
district_id (FK -> districts.id)
state_id (FK -> states.id)
capacity (INT, NULLABLE)
description (TEXT, NULLABLE)
contact_person (VARCHAR, NULLABLE)
contact_phone (VARCHAR, NULLABLE)
is_available (BOOLEAN, DEFAULT true)
image (VARCHAR, NULLABLE)
created_at, updated_at
```

## League Management

### leagues
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
slug (VARCHAR, UNIQUE)
game_id (FK -> games.id)
season (TINYINT)
start_date (DATE)
end_date (DATE)
max_teams (INT)
max_team_players (INT)
team_reg_fee (DOUBLE)
player_reg_fee (DOUBLE)
retention (BOOLEAN, DEFAULT false)
retention_players (INT, DEFAULT 0)
team_wallet_limit (DOUBLE)
is_default (BOOLEAN, DEFAULT false)
status (ENUM: pending, active, completed, cancelled, auction_completed)
auction_active (BOOLEAN, DEFAULT false)
bid_increment_type (VARCHAR, NULLABLE)
custom_bid_increment (INT, NULLABLE)
predefined_increments (JSON, NULLABLE)
logo (VARCHAR, NULLABLE)
banner (VARCHAR, NULLABLE)
prize_pool (DECIMAL, NULLABLE)
winner_team_id (FK -> league_teams.id, NULLABLE)
runner_team_id (FK -> league_teams.id, NULLABLE)
auction_access (ENUM: organizer_only, team_owners, auctioneers)
created_at, updated_at
```

### league_organizers
```sql
id (PK, AUTO_INCREMENT)
user_id (FK -> users.id)
league_id (FK -> leagues.id)
status (ENUM: pending, approved, rejected)
message (TEXT, NULLABLE)
admin_notes (TEXT, NULLABLE)
created_at, updated_at
UNIQUE(user_id, league_id)
```

### teams
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
slug (VARCHAR, UNIQUE)
owner_id (FK -> users.id)
logo (VARCHAR, NULLABLE)
banner (VARCHAR, NULLABLE)
home_ground_id (FK -> grounds.id)
local_body_id (FK -> local_bodies.id)
created_by (FK -> users.id)
created_at, updated_at
```

### team_owners
```sql
id (PK, AUTO_INCREMENT)
team_id (FK -> teams.id)
user_id (FK -> users.id)
role (ENUM: owner, co_owner)
created_at, updated_at
UNIQUE(team_id, user_id)
```

### league_teams
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
team_id (FK -> teams.id)
slug (VARCHAR, UNIQUE)
status (ENUM: pending, available)
wallet_balance (DOUBLE, DEFAULT 0.0)
auctioneer_id (FK -> users.id, NULLABLE)
created_at, updated_at
UNIQUE(league_id, team_id)
```

### team_auctioneers
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
team_id (FK -> teams.id)
league_team_id (FK -> league_teams.id)
auctioneer_id (FK -> users.id)
status (ENUM: active, inactive)
notes (TEXT, NULLABLE)
created_at, updated_at
UNIQUE(league_id, auctioneer_id)
```

## Player Management

### user_game_roles
```sql
id (PK, AUTO_INCREMENT)
user_id (FK -> users.id)
game_id (FK -> games.id)
game_position_id (FK -> game_positions.id)
is_primary (BOOLEAN, DEFAULT false)
created_at, updated_at
UNIQUE(user_id, game_id)
UNIQUE(user_id, is_primary)
```

### league_players
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
league_team_id (FK -> league_teams.id, NULLABLE)
user_id (FK -> users.id)
slug (VARCHAR, UNIQUE)
retention (BOOLEAN, DEFAULT false)
status (ENUM: pending, available, auctioning, sold, unsold, skip)
base_price (DOUBLE, DEFAULT 0.0)
bid_price (DOUBLE, NULLABLE)
created_at, updated_at
UNIQUE(league_id, user_id)
```

## Auction System

### auctions
```sql
id (PK, AUTO_INCREMENT)
league_player_id (FK -> league_players.id)
league_team_id (FK -> league_teams.id)
amount (DECIMAL)
status (ENUM: won, ask, lost, refunded)
created_at, updated_at
```

## Tournament Structure

### league_groups
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
name (VARCHAR)
slug (VARCHAR, UNIQUE)
sort_order (INT, DEFAULT 0)
created_at, updated_at
UNIQUE(league_id, name)
```

### league_group_teams
```sql
id (PK, AUTO_INCREMENT)
league_group_id (FK -> league_groups.id)
league_team_id (FK -> league_teams.id)
created_at, updated_at
UNIQUE(league_group_id, league_team_id)
```

### fixtures
```sql
id (PK, AUTO_INCREMENT)
slug (VARCHAR, UNIQUE)
league_id (FK -> leagues.id)
home_team_id (FK -> league_teams.id, NULLABLE)
away_team_id (FK -> league_teams.id, NULLABLE)
league_group_id (FK -> league_groups.id, NULLABLE)
match_type (ENUM: group_stage, quarter_final, semi_final, final)
status (ENUM: unscheduled, scheduled, in_progress, completed, cancelled)
match_date (DATE, NULLABLE)
match_time (TIME, NULLABLE)
venue (VARCHAR, NULLABLE)
home_score (INT, NULLABLE)
away_score (INT, NULLABLE)
notes (TEXT, NULLABLE)
created_at, updated_at
```

## Financial Management

### expense_categories
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
description (TEXT, NULLABLE)
created_at, updated_at
```

### league_finances
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
expense_category_id (FK -> expense_categories.id)
user_id (FK -> users.id)
title (VARCHAR)
description (TEXT, NULLABLE)
amount (DECIMAL)
type (ENUM: income, expense)
transaction_date (DATE)
reference_number (VARCHAR, NULLABLE)
attachment (VARCHAR, NULLABLE)
created_at, updated_at
```

## System Tables

### roles
```sql
id (PK, AUTO_INCREMENT)
name (VARCHAR)
created_at, updated_at
```

### user_roles
```sql
id (PK, AUTO_INCREMENT)
user_id (FK -> users.id)
role_id (FK -> roles.id)
created_at, updated_at
```

### organizer_requests
```sql
id (PK, AUTO_INCREMENT)
user_id (FK -> users.id)
message (TEXT, NULLABLE)
status (ENUM: pending, approved, rejected)
admin_notes (TEXT, NULLABLE)
slug (VARCHAR, NULLABLE)
created_at, updated_at
```

### notifications
```sql
id (PK, AUTO_INCREMENT)
type (VARCHAR)
notifiable_type (VARCHAR)
notifiable_id (BIGINT)
data (TEXT)
read_at (TIMESTAMP, NULLABLE)
created_at, updated_at
```

### league_grounds
```sql
id (PK, AUTO_INCREMENT)
league_id (FK -> leagues.id)
ground_id (FK -> grounds.id)
created_at, updated_at
UNIQUE(league_id, ground_id)
```

## Laravel System Tables

### cache
```sql
key (VARCHAR, PRIMARY)
value (MEDIUMTEXT)
expiration (INT)
```

### jobs
```sql
id (PK, AUTO_INCREMENT)
queue (VARCHAR)
payload (LONGTEXT)
attempts (TINYINT)
reserved_at (INT, NULLABLE)
available_at (INT)
created_at (INT)
```

### personal_access_tokens
```sql
id (PK, AUTO_INCREMENT)
tokenable_type (VARCHAR)
tokenable_id (BIGINT)
name (VARCHAR)
token (VARCHAR, UNIQUE)
abilities (TEXT, NULLABLE)
last_used_at (TIMESTAMP, NULLABLE)
expires_at (TIMESTAMP, NULLABLE)
created_at, updated_at
```

### password_reset_tokens
```sql
email (VARCHAR, PRIMARY)
token (VARCHAR)
created_at (TIMESTAMP, NULLABLE)
```

### sessions
```sql
id (VARCHAR, PRIMARY)
user_id (FK -> users.id, NULLABLE)
ip_address (VARCHAR, NULLABLE)
user_agent (TEXT, NULLABLE)
payload (LONGTEXT)
last_activity (INT)
```