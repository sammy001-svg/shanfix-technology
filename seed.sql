-- Admin User Seed Data
-- Email: info@shanfixtechnology.com
-- Password: Sam@123@1s

INSERT INTO `users` (`full_name`, `email`, `password`, `role`, `status`) 
VALUES (
    'Shanfix Administrator', 
    'info@shanfixtechnology.com', 
    '$2y$10$lkPZLgfOI3n1HV6plD2DNu.VlbGEDjkIxdHI/sM0qyB0QGz1ooJsG', 
    'admin', 
    'active'
) 
ON DUPLICATE KEY UPDATE 
    `password` = VALUES(`password`),
    `role` = 'admin',
    `status` = 'active';
