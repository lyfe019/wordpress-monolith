<?php
namespace Platinum\Core\Modules;

final class ModuleLoader
{
    private array $modules = [];
    private array $loaded  = [];

    public function add(ModuleInterface $module): void
    {
        $id = $module->id();

        if (isset($this->modules[$id])) {
            error_log("❌ ModuleLoader: duplicate module id [$id]");
            throw new \RuntimeException("Duplicate module id [$id]");
        }

        error_log("➕ ModuleLoader: module added [$id]");
        $this->modules[$id] = $module;
    }

    public function boot(): void
    {
        error_log('🚀 ModuleLoader: boot sequence started');

        // -----------------------------
        // Phase 1 — Registration
        // -----------------------------
        foreach ($this->modules as $id => $module) {
            error_log("🧩 ModuleLoader: registering [$id]");
            $module->register();
        }

        // -----------------------------
        // Phase 2 — Booting
        // -----------------------------
        foreach ($this->modules as $id => $module) {
            $this->bootModule($module);
        }

        error_log('✅ ModuleLoader: boot sequence completed');
    }

    private function bootModule(ModuleInterface $module): void
    {
        $id = $module->id();

        if (isset($this->loaded[$id])) {
            return;
        }

        // -----------------------------
        // Dependency resolution
        // -----------------------------
        foreach ($module->dependencies() as $dependencyId) {
            if (!isset($this->modules[$dependencyId])) {
                error_log("❌ ModuleLoader: missing dependency [$dependencyId] for [$id]");
                throw new \RuntimeException(
                    "Module [$id] depends on missing module [$dependencyId]"
                );
            }

            $this->bootModule($this->modules[$dependencyId]);
        }

        error_log("🔥 ModuleLoader: booting [$id]");
        $module->boot();

        $this->loaded[$id] = true;
    }
}
