<?php

class Info
{
    public const HOST = $_ENV['DB_HOST'] ?? 'mariadb';
    public const DB_NAME = $_ENV['DB_NAME'] ?? 'addresses';
    public const USER = $_ENV['DB_USER'] ?? 'root';
    public const PASSWORD = $_ENV['DB_PASSWORD'] ?? 'password';
}