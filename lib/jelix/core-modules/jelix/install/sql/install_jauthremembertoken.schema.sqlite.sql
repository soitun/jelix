
CREATE TABLE IF NOT EXISTS %%PREFIX%%jauthremembertoken (
  token_hash varchar(128) NOT NULL,
  series_hash varchar(128) NOT NULL,
  login varchar(255) NOT NULL,
  expires_at integer DEFAULT 0 NOT NULL,
  created_at datetime NOT NULL,
  series_id integer DEFAULT 0,
  PRIMARY KEY  (login, token_hash)
);

CREATE INDEX IF NOT EXISTS %%PREFIX%%jauthremembertoken_login_series_hash_idx ON %%PREFIX%%jauthremembertoken (login, series_hash);
