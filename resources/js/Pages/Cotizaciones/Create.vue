<script setup>
import { reactive, computed } from "vue";
import { router } from "@inertiajs/vue3";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from "@/components/ui/number-field";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Label } from "@/components/ui/label";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Separator } from "@/components/ui/separator";
import { Loader2, Trash, ArrowLeft } from "lucide-vue-next";
import jsPDF from "jspdf";
import "jspdf-autotable";
import { Toaster } from '@/components/ui/sonner'
import { toast } from 'vue-sonner'
// Props desde el backend
const props = defineProps({
    clientes: Array,
    productos: Array,
});

// Objeto reactivo para manejar datos del formulario
const form = reactive({
    cliente_id: null,
    productos: [],
    cantidades: {},
    preciosSeleccionados: {},
    busqueda: "",
    total: 0,
});
// Estado para agregar un nuevo cliente
const nuevoCliente = reactive({
    nombre: "",
    showDialog: false,
});
// Computed para productos filtrados
const productosFiltrados = computed(() =>
    form.busqueda
        ? props.productos.filter((producto) =>
            producto.nombre.toLowerCase().includes(form.busqueda.toLowerCase())
        )
        : props.productos
);

// Agregar producto al carrito
function agregarProducto(producto) {
    const precioSeleccionadoId = form.preciosSeleccionados[producto.id];
    const precioSeleccionado = producto.precios_stocks.find((p) => p.id == precioSeleccionadoId);
    const cantidad = form.cantidades[producto.id] || 0;

    if (precioSeleccionado && cantidad > 0 && cantidad <= precioSeleccionado.stock) {
        const existente = form.productos.find(
            (item) => item.id == producto.id && item.precio_stock_id == precioSeleccionadoId
        );
        if (existente) {
            existente.cantidad += cantidad;
            existente.subtotal += precioSeleccionado.precio * cantidad;
        } else {
            form.productos.push({
                id: producto.id,
                nombre: producto.nombre,
                precio_stock_id: precioSeleccionadoId,
                cantidad,
                subtotal: precioSeleccionado.precio * cantidad,
            });
        }
        form.cantidades[producto.id] = 0;
        calcularTotal();
    }
}

// Eliminar producto del carrito
function eliminarProducto(productoId, precioStockId) {
    form.productos = form.productos.filter(
        (item) => !(item.id == productoId && item.precio_stock_id == precioStockId)
    );
    calcularTotal();
}

// Calcular total de la cotización
function calcularTotal() {
    form.total = form.productos.reduce((acc, item) => acc + item.subtotal, 0);
}

// Guardar la cotización
function guardarCotizacion() {
    router.post(
        "/cotizaciones",
        {
            cliente_id: form.cliente_id,
            productos: form.productos.map(({ id, precio_stock_id, cantidad }) => ({
                id,
                precio_stock_id,
                cantidad,
            })),
            total: form.total,
        },
        {
            onSuccess: () => {
                toast.success("Cotización guardada exitosamente.");
                form.productos = []; // Limpiar el formulario
                form.total = 0;
                form.cliente_id = null;
            },
            onError: (error) => {
                toast.error("Hubo un error al guardar la cotización.");
                console.error(error);
            },
        }
    );
}

// Generar PDF de la cotización
function generarPDF() {
    if (!form.productos.length || !form.cliente_id) return;

    const doc = new jsPDF();

    // Agregar encabezados
    const clienteActual = props.clientes.find((cliente) => cliente.id == form.cliente_id);
    doc.setFontSize(18);
    doc.text("Cotización", 14, 22);

    doc.setFontSize(12);
    doc.text(`Cliente: ${clienteActual?.nombre || "No seleccionado"}`, 14, 30);
    doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 14, 38);

    // Agregar detalles de productos
    const tableColumn = ["Producto", "Precio", "Cantidad", "Subtotal"];
    const tableRows = form.productos.map((item) => [
        item.nombre,
        `$${(item.subtotal / item.cantidad).toFixed(2)}`,
        item.cantidad,
        `$${item.subtotal.toFixed(2)}`,
    ]);

    doc.autoTable({
        startY: 45,
        head: [tableColumn],
        body: tableRows,
    });

    // Total
    const finalY = doc.lastAutoTable.finalY || 45;
    doc.text(`Total: $${form.total.toFixed(2)}`, 14, finalY + 10);

    // Descargar PDF
    doc.save("cotizacion.pdf");
}

// Agregar un nuevo cliente
function agregarNuevoCliente() {
    if (!nuevoCliente.nombre.trim()) return;

    router.post("/clientes", { nombre: nuevoCliente.nombre }, {
        onSuccess: (response) => {
            // Actualizar lista de clientes
            props.clientes = response.props.cliente;
            nuevoCliente.nombre = ""; // Limpiar el formulario
            nuevoCliente.showDialog = false; // Cerrar el diálogo
            toast.success("Cliente agregado exitosamente.");

        },
    });
}

function volverAtras() {
    router.visit('/'); 
}
</script>

<template>
    <div class="container-fluid mx-12 my-12 p-4">
        <div class="flex items-center space-x-4 mb-6">
            <Button variant="outline" size="icon" @click="volverAtras" aria-label="Volver atrás">
                <ArrowLeft class="w-6 h-6" />
            </Button>
            <h1 class="text-3xl font-bold">Crear Cotización</h1>
        </div>
        <div class="grid md:grid-cols-2 gap-2">
            <div>

                <!-- Seleccionar Cliente -->
                <Card class="mb-6">
                    <CardHeader>
                        <CardTitle>Seleccionar Cliente</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center space-x-4">

                            <Select v-model="form.cliente_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Seleccione un cliente" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="cliente in props.clientes" :key="cliente.id"
                                        :value="cliente.id.toString()">
                                        {{ cliente.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <Dialog v-model:open="nuevoCliente.showDialog">
                                <DialogTrigger>
                                    <Button variant="outline">Agregar Nuevo Cliente</Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Agregar Nuevo Cliente</DialogTitle>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <Label htmlFor="nombre" class="text-right">
                                                Nombre
                                            </Label>
                                            <Input id="nombre" v-model="nuevoCliente.nombre" class="col-span-3" />
                                        </div>
                                    </div>
                                    <Button @click="agregarNuevoCliente">Guardar</Button>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </CardContent>
                </Card>

                <!-- Buscar Productos -->
                <Card>
                    <CardHeader>
                        <CardTitle>Productos Disponibles</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4">
                            <Input v-model="form.busqueda" type="text" placeholder="Buscar producto..."
                                class="w-full" />
                        </div>
                        <div class="overflow-y-auto max-h-96">
                            <Table>
                                <TableHeader class="sticky top-0 bg-white z-10 shadow">
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Precio</TableHead>
                                        <TableHead>Stock</TableHead>
                                        <TableHead>Cantidad</TableHead>
                                        <TableHead></TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="producto in productosFiltrados" :key="producto.id">
                                        <TableCell>{{ producto.nombre }}</TableCell>
                                        <TableCell>
                                            <Select v-model="form.preciosSeleccionados[producto.id]">
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Seleccione un precio" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem v-for="precio in producto.precios_stocks"
                                                        :key="precio.id" :value="precio.id.toString()">
                                                        ${{ precio.precio }} (Stock: {{ precio.stock }})
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </TableCell>
                                        <TableCell>
                                            {{
                                                form.preciosSeleccionados[producto.id]
                                                    ? producto.precios_stocks.find(
                                                        (p) => p.id == form.preciosSeleccionados[producto.id]
                                                    )?.stock
                                                    : "Seleccione un precio"
                                            }}
                                        </TableCell>
                                        <TableCell>
                                            <NumberField v-model.number="form.cantidades[producto.id]"
                                                :default-value="0" :min="1">
                                                <NumberFieldContent>
                                                    <NumberFieldDecrement />
                                                    <NumberFieldInput />
                                                    <NumberFieldIncrement />
                                                </NumberFieldContent>
                                            </NumberField>
                                        </TableCell>
                                        <TableCell>
                                            <Button @click="agregarProducto(producto)"
                                                :disabled="!form.preciosSeleccionados[producto.id] || !form.cantidades[producto.id]">
                                                Agregar
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Resumen de Cotización -->
            <Card class="flex flex-col h-full ">
                <CardHeader>
                    <CardTitle>Resumen de Cotización</CardTitle>
                </CardHeader>
                <CardContent class="flex-1 overflow-y-auto">
                    <div class="overflow-y-auto max-h-[calc(100vh-33rem)] ">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Producto</TableHead>
                                    <TableHead>Precio</TableHead>
                                    <TableHead>Cantidad</TableHead>
                                    <TableHead>Subtotal</TableHead>
                                    <TableHead></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="form.productos.length" v-for="item in form.productos"
                                    :key="`${item.id}-${item.precio_stock_id}`">
                                    <TableCell>{{ item.nombre }}</TableCell>
                                    <TableCell>${{ (item.subtotal / item.cantidad).toFixed(2) }}</TableCell>
                                    <TableCell>{{ item.cantidad }}</TableCell>
                                    <TableCell>${{ item.subtotal.toFixed(2) }}</TableCell>
                                    <TableCell>
                                        <Button variant="destructive" size="sm"
                                            @click="eliminarProducto(item.id, item.precio_stock_id)">
                                            Eliminar
                                        </Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else>
                                    <TableCell colspan="5" class="text-center text-xl font-semibold">No hay productos en
                                        el carrito</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div class="mt-4 text-right">
                        <strong>Total: ${{ form.total.toFixed(2) }}</strong>
                    </div>
                </CardContent>
                <div class="p-4">
                    <Button class="w-full" @click="guardarCotizacion"
                        :disabled="!form.productos.length || !form.cliente_id">
                        Guardar Cotización
                    </Button>
                    <Separator label="O" class="my-4" />
                    <Button class="w-full" @click="generarPDF" variant="destructive"
                        :disabled="!form.productos.length || !form.cliente_id">
                        Descargar PDF
                    </Button>
                </div>
            </Card>
        </div>
    </div>
    <Toaster />

</template>
