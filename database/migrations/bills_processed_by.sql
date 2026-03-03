-- Track which staff member processed/finalized each bill payment
ALTER TABLE bills
  ADD COLUMN IF NOT EXISTS processed_by INT NULL AFTER payment_status;

-- Back-fill existing paid bills with the user who logged the payment from audit_logs
UPDATE bills b
SET b.processed_by = (
    SELECT al.user_id
    FROM audit_logs al
    WHERE al.module = 'billing'
      AND al.action = 'update'
      AND al.details LIKE CONCAT('%bill: ', b.id)
    ORDER BY al.created_at DESC
    LIMIT 1
)
WHERE b.payment_status = 'paid'
  AND b.processed_by IS NULL;
