import { openDB } from 'idb';

const DB_NAME = 'ventas_offline_db';
const SALES_STORE = 'sales';
const SYNC_STORE = 'pending_syncs';
const VERSION = 2;

export async function initDB() {
    return openDB(DB_NAME, VERSION, {
        upgrade(db, oldVersion, newVersion) {
            if (!db.objectStoreNames.contains(SALES_STORE)) {
                db.createObjectStore(SALES_STORE, { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains(SYNC_STORE)) {
                db.createObjectStore(SYNC_STORE, { keyPath: 'id', autoIncrement: true });
            }
        },
    });
}

// Ventas Específicas
export async function saveOfflineSale(saleData) {
    const db = await initDB();
    const tx = db.transaction(SALES_STORE, 'readwrite');
    const store = tx.objectStore(SALES_STORE);
    
    const sale = {
        ...saleData,
        created_at: new Date().toISOString(),
        synced: false
    };
    
    await store.add(sale);
    await tx.done;
}

export async function getPendingSales() {
    const db = await initDB();
    const tx = db.transaction(SALES_STORE, 'readonly');
    const store = tx.objectStore(SALES_STORE);
    const allSales = await store.getAll();
    return allSales.filter(sale => !sale.synced);
}

export async function markSaleAsSynced(id) {
    const db = await initDB();
    const tx = db.transaction(SALES_STORE, 'readwrite');
    const store = tx.objectStore(SALES_STORE);
    const sale = await store.get(id);
    if (sale) {
        sale.synced = true;
        await store.put(sale);
    }
    await tx.done;
}

export async function deleteSyncedSales() {
    const db = await initDB();
    const tx = db.transaction(SALES_STORE, 'readwrite');
    const store = tx.objectStore(SALES_STORE);
    const allSales = await store.getAll();
    for (const sale of allSales) {
        if (sale.synced) {
            await store.delete(sale.id);
        }
    }
    await tx.done;
}

// Sincronización Genérica (Productos, Clientes, etc.)
export async function queuePendingSync(syncData) {
    const db = await initDB();
    const tx = db.transaction(SYNC_STORE, 'readwrite');
    const store = tx.objectStore(SYNC_STORE);
    
    const item = {
        ...syncData,
        created_at: new Date().toISOString(),
        synced: false
    };
    
    const id = await store.add(item);
    await tx.done;
    return id;
}

export async function getPendingSyncs() {
    const db = await initDB();
    const tx = db.transaction(SYNC_STORE, 'readonly');
    const store = tx.objectStore(SYNC_STORE);
    const all = await store.getAll();
    return all.filter(item => !item.synced);
}

export async function markSyncAsDone(id) {
    const db = await initDB();
    const tx = db.transaction(SYNC_STORE, 'readwrite');
    const store = tx.objectStore(SYNC_STORE);
    await store.delete(id);
    await tx.done;
}
