<template>
    <div class="container mx-auto my-12 p-4 shadow">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Listado de Cotizaciones</h1>
            <Link href="cotizaciones/create"
                class="bg-black text-white px-4 py-2 rounded hover:bg-black/80 transition-colors">
            Crear Cotización
            </Link>
        </div>

        <!-- Tabla de cotizaciones -->
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Cliente</TableHead>
                    <TableHead>Total</TableHead>
                    <TableHead>Fecha</TableHead>
                    <TableHead>Acciones</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="cotizacion in cotizaciones" :key="cotizacion.id">
                    <TableCell>{{ cotizacion.cliente.nombre }}</TableCell>
                    <TableCell>${{ cotizacion.total.toFixed(2) }}</TableCell>
                    <TableCell>{{ new Date(cotizacion.created_at).toLocaleDateString() }}</TableCell>
                    <TableCell>
                        <Button @click="descargarCotizacion(cotizacion)" variant="outline">
                            Descargar PDF
                        </Button>
                    </TableCell>
                </TableRow>
                <TableRow v-if="cotizaciones.length === 0">
                    <TableCell colspan="4" class="text-center text-xl font-semibold">No hay cotizaciones</TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
    <Toaster />
</template>

<script setup >
import { Link } from '@inertiajs/vue3'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Toaster } from '@/components/ui/sonner'
import { Button } from "@/components/ui/button";
import jsPDF from "jspdf";
import "jspdf-autotable";

defineProps({ cotizaciones: Object })


function descargarCotizacion(cotizacion) {
    const doc = new jsPDF();

    // Encabezado
    doc.setFontSize(18);
    doc.text("Cotización", 14, 22);

    doc.setFontSize(12);
    doc.text(`Cliente: ${cotizacion.cliente.nombre}`, 14, 30);
    doc.text(`Fecha: ${new Date(cotizacion.created_at).toLocaleDateString()}`, 14, 38);

    // Agregar detalles de los productos
    const tableColumn = ["Producto", "Precio Unitario", "Cantidad", "Subtotal"];
    const tableRows = cotizacion.detalles.map((detalle) => {
        const producto = detalle.producto?.nombre || "Producto no encontrado";
        const precio = detalle.precio_stock?.precio?.toFixed(2) || "0.00";
        const cantidad = detalle.cantidad || 0;
        const subtotal = detalle.subtotal?.toFixed(2) || "0.00";

        return [producto, `$${precio}`, cantidad.toString(), `$${subtotal}`];
    });

    doc.autoTable({
        startY: 45,
        head: [tableColumn],
        body: tableRows,
    });

    // Total
    const finalY = (doc).lastAutoTable.finalY || 45;
    doc.text(`Total: $${cotizacion.total.toFixed(2)}`, 14, finalY + 10);

    // Guardar el PDF
    doc.save(`Cotizacion_${cotizacion.id}.pdf`);
}

</script>
