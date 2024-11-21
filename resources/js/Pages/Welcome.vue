<template>
    <div class="container mx-auto p-4">
      <h1 class="text-3xl font-bold mb-6">Pantalla de Cotización</h1>
  
      <!-- Seleccionar Cliente -->
      <Card class="mb-6">
        <CardHeader>
          <CardTitle>Seleccionar Cliente</CardTitle>
        </CardHeader>
        <CardContent>
          <Select v-model="selectedClient">
            <SelectTrigger>
              <SelectValue placeholder="Seleccione un cliente" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="cliente in clientes"
                :key="cliente.id"
                :value="cliente.id"
              >
                {{ cliente.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </CardContent>
      </Card>
  
      <!-- Productos y Carrito -->
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Productos Disponibles -->
        <Card>
          <CardHeader>
            <CardTitle>Productos Disponibles</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Nombre</TableHead>
                  <TableHead>Precio</TableHead>
                  <TableHead>Stock</TableHead>
                  <TableHead>Cantidad</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="producto in productos" :key="producto.id">
                  <TableCell>{{ producto.name }}</TableCell>
                  <TableCell>${{ producto.price }}</TableCell>
                  <TableCell>{{ producto.stock }}</TableCell>
                  <TableCell>
                    <Input
                      type="number"
                      v-model.number="cantidad[producto.id]"
                      :min="1"
                      :max="producto.stock"
                      placeholder="0"
                      class="w-20"
                    />
                  </TableCell>
                  <TableCell>
                    <Button @click="agregarProducto(producto)">Agregar</Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>
  
        <!-- Carrito -->
        <Card>
          <CardHeader>
            <CardTitle>Carrito de Cotización</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Nombre</TableHead>
                  <TableHead>Precio</TableHead>
                  <TableHead>Cantidad</TableHead>
                  <TableHead>Subtotal</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="item in carrito" :key="item.id">
                  <TableCell>{{ item.name }}</TableCell>
                  <TableCell>${{ item.price }}</TableCell>
                  <TableCell>{{ item.cantidad }}</TableCell>
                  <TableCell>${{ item.price * item.cantidad }}</TableCell>
                  <TableCell>
                    <Button variant="destructive" @click="eliminarProducto(item.id)">
                      Eliminar
                    </Button>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
            <div class="mt-4 text-right">
              <strong>Total: ${{ total }}</strong>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </template>
  
  <script lang="ts" setup>
  import { ref, computed, onMounted } from "vue";
  import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
  } from "@/components/ui/select";
  import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Button } from "@/components/ui/button";
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
  
  // Variables reactivas
  const clientes = ref([]);
  const productos = ref([]);
  const selectedClient = ref<string | null>(null);
  const carrito = ref<Array<any>>([]);
  const cantidad = ref<Record<string, number>>({});
  
  // Fetch de clientes desde la API
  const fetchClientes = async () => {
    try {
      const response = await fetch("/clientes");
      if (!response.ok) throw new Error("Error al obtener clientes");
      clientes.value = await response.json();
    } catch (error) {
      console.error(error);
    }
  };
  
  // Fetch de productos desde la API
  const fetchProductos = async () => {
    try {
      const response = await fetch("/productos");
      if (!response.ok) throw new Error("Error al obtener productos");
      productos.value = await response.json();
    } catch (error) {
      console.error(error);
    }
  };
  
  // Agregar producto al carrito
  const agregarProducto = (producto: any) => {
    const cantidadSeleccionada = cantidad.value[producto.id] || 0;
    if (cantidadSeleccionada > 0 && cantidadSeleccionada <= producto.stock) {
      const productoEnCarrito = carrito.value.find(
        (item) => item.id === producto.id
      );
      if (productoEnCarrito) {
        productoEnCarrito.cantidad += cantidadSeleccionada;
      } else {
        carrito.value.push({
          ...producto,
          cantidad: cantidadSeleccionada,
        });
      }
      cantidad.value[producto.id] = 0; // Reiniciar la cantidad
    }
  };
  
  // Eliminar producto del carrito
  const eliminarProducto = (productoId: string) => {
    carrito.value = carrito.value.filter((item) => item.id !== productoId);
  };
  
  // Calcular el total
  const total = computed(() =>
    carrito.value.reduce((sum, item) => sum + item.price * item.cantidad, 0)
  );
  
  // Cargar los datos al montar el componente
  onMounted(() => {
    fetchClientes();
    fetchProductos();
  });
  </script>
  