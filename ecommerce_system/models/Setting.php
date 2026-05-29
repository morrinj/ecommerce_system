<?php
require_once __DIR__ . '/BaseModel.php';

class Setting extends BaseModel {
    protected string $table = 'settings';

    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM settings ORDER BY group_name ASC, setting_key ASC");
        return $stmt->fetchAll();
    }

    public function getByGroup(string $group): array {
        $stmt = $this->db->prepare("SELECT * FROM settings WHERE group_name = ? ORDER BY setting_key ASC");
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }

    public function get(string $key): ?string {
        $result = $this->findBy('setting_key', $key);
        return $result ? $result['setting_value'] : null;
    }

    public function set(string $key, string $value): bool {
        $existing = $this->findBy('setting_key', $key);
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        }
        return (bool) $this->create([
            'setting_key' => $key,
            'setting_value' => $value,
        ]);
    }

    public function updateBatch(array $settings): void {
        $this->beginTransaction();
        try {
            foreach ($settings as $key => $value) {
                $this->set($key, $value);
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
