# AGENTS.md - import-cli-simple

## Zweck & Verantwortung

Das `import-cli-simple` Modul ist die **Master CLI-Anwendung** für das Pacemaker Import-System. Es ist ein **Tier 7 Modul** und integriert **ALLE 38 anderen Module**.

**Hauptverantwortung:**
- Vollständige CLI für Single-Threaded Imports
- Integration aller Importer (Attribute, Category, Customer, Product, etc.)
- Integration aller Converter Module
- Integration aller EE Features
- Integration aller MSI Features
- Zentrale Entry Point für die meisten Use-Cases

## Architektur & Design Patterns

### Kern-Klassen
- **SimpleCliApplication**: Haupt-CLI-Anwendung
- **ImportCommand**: Haupt-Import-Command
- **ConfigurationLoader**: Konfiguration-Loader

### Verwendete Patterns
- **Command Pattern**: Für CLI Commands
- **Facade Pattern**: Vereinfachte Schnittstelle für alle Module
- **Dependency Injection**: Für Dependency Management

## Abhängigkeiten

### Externe Pakete
- **egulias/email-validator** ^1.0|^2.0|^3.0|^4.0 - Email-Validierung

### TechDivision Dependencies (35 Module!)
**Tier 3-4 (Core):**
- **import** ^18.1 - Core Framework
- **import-cli** ^13.1 - CLI Framework
- **import-app-simple** ^19.0 - Simple Application
- **import-configuration-jms** ^18.1 - JMS Configuration

**Tier 4 (Entity Importers):**
- **import-attribute** ^23.1 - Attribute Importer
- **import-category** ^22.1 - Category Importer
- **import-customer** ^18.1 - Customer Importer
- **import-product** ^26.2 - Product Importer
- **import-converter** ^12.0 - Converter Framework
- **import-ee** ^17.0 - EE Functionality

**Tier 5 (Specialized Importers):**
- **import-attribute-set** ^18.1 - Attribute Set Importer
- **import-customer-address** ^18.1 - Customer Address Importer
- **import-product-bundle** ^26.1 - Bundle Product Importer
- **import-product-grouped** ^20.1 - Grouped Product Importer
- **import-product-link** ^26.1 - Product Link Importer
- **import-product-media** ^28.1 - Product Media Importer
- **import-product-msi** ^21.1 - MSI Stock Importer
- **import-product-tier-price** ^19.1 - Tier Price Importer
- **import-product-url-rewrite** ^26.1 - URL Rewrite Importer
- **import-product-variant** ^26.1 - Configurable Product Importer

**Tier 5 (Converters):**
- **import-converter-customer-attribute** ^4.1 - Customer Attribute Converter
- **import-converter-product-attribute** ^11.1 - Product Attribute Converter
- **import-converter-product-category** ^11.0 - Product Category Converter
- **import-converter-ee** ^12.0 - EE Converter

**Tier 6 (EE Extensions):**
- **import-category-ee** ^23.0 - EE Category Extensions
- **import-product-ee** ^27.2 - EE Product Extensions
- **import-product-bundle-ee** ^28.0 - EE Bundle Extensions
- **import-product-grouped-ee** ^22.0 - EE Grouped Extensions
- **import-product-link-ee** ^28.0 - EE Link Extensions
- **import-product-media-ee** ^29.0 - EE Media Extensions
- **import-product-variant-ee** ^28.0 - EE Variant Extensions

**Infrastructure (Tier 0-2):**
- **import-cache** ^2.0 - Cache Interfaces
- **import-dbal** ^2.0 - DBAL Interfaces
- **import-serializer** ^2.1 - Serializer Interfaces
- **import-dbal-collection** ^2.1 - DBAL Implementation
- **import-cache-collection** ^2.0 - Cache Implementation
- **import-serializer-csv** ^2.1 - CSV Serializer
- **import-configuration** ^6.1 - Configuration Interfaces

### Abhängig von diesem Modul
- **Keine** - Master CLI, kein Dependent

## Wichtige Entry Points

### CLI Application
```php
// Simple CLI Application
SimpleCliApplication::run(): void
SimpleCliApplication::execute($operation): void

// Import Command
ImportCommand::execute(InputInterface $input, OutputInterface $output): int
```

### Verwendungsbeispiel
```bash
# Attribute Import
bin/magento import:attribute config.xml

# Category Import
bin/magento import:category config.xml

# Product Import (mit allen Variants)
bin/magento import:product config.xml

# Vollständiger Import (alle Entities)
bin/magento import:all config.xml
```

## Events & Extension Points

**Keine Events** - Tier 7 CLI-Modul

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 7 Modul**: Master CLI mit allen 38 anderen Modulen
2. **35 TechDivision Dependencies**: Integriert alle Importer
3. **Facade Pattern**: Vereinfachte Schnittstelle für alle Features
4. **Single-Threaded**: Für Single-Threaded Imports
5. **Entry Point**: Zentrale Entry Point für die meisten Use-Cases

### Architektur-Übersicht
```
import-cli-simple (Master CLI)
  ├─ Tier 3-4: Core Framework (import, import-cli, import-app-simple)
  ├─ Tier 4: Entity Importers (attribute, category, customer, product, converter, ee)
  ├─ Tier 5: Specialized Importers (bundle, grouped, link, media, msi, tier-price, url-rewrite, variant)
  ├─ Tier 5: Converters (customer-attr, product-attr, product-category, ee)
  ├─ Tier 6: EE Extensions (category-ee, product-ee, bundle-ee, grouped-ee, link-ee, media-ee, variant-ee)
  └─ Tier 0-2: Infrastructure (cache, dbal, serializer, configuration)
```

### Bei Änderungen
- **Breaking Changes**: Beachte alle 35 Dependencies
- **Backward Compatibility**: Alte Imports sollten noch funktionieren
- **CLI-Kompatibilität**: Beachte CLI-Interface

### Implementierungs-Hinweise
- Nutze Facade Pattern für einfache Schnittstelle
- Beachte Dependency-Reihenfolge
- Erwäge Performance bei großen Imports

## Bekannte Einschränkungen

- **Single-Threaded**: Nicht für Multi-Threaded Imports
- **Memory-Intensive**: Große Datenmengen können Memory-Probleme verursachen
- **Keine Transaktionen**: Transaktions-Handling erfolgt in Importern
- **Keine Rollback**: Fehler können zu Daten-Inkonsistenzen führen

## Zusammenfassung

`import-cli-simple` ist das **Master CLI-Modul** des Pacemaker-Systems. Es integriert **ALLE 38 anderen Module** und bietet eine vollständige CLI für Single-Threaded Imports. Es ist die zentrale Entry Point für die meisten Use-Cases.

**Für Agenten:** Verstehe dieses Modul als **Master CLI** mit 35 TechDivision Dependencies, Facade Pattern, und vollständiger Integration aller Import-Features.

**Kritisch:** Dieses Modul ist das Herzstück des Pacemaker-Systems. Änderungen hier können alle 35 Dependents beeinflussen!
