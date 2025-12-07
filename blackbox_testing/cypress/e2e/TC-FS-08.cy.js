describe("TC-FS-08", () => {
    it("TC-FS-08", () => {
        cy.login();
        cy.viewport(1089, 825);

        cy.visit("/apps/files/files?dir=/fake/path.docx", { failOnStatusCode: false });

        cy.contains("File not found", { timeout: 5000 })
            .or(".error-message")
            .should("be.visible");
    });
});